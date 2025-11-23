# SPEC – Database Design & User Flows for Costing, Tariff, and Clinical Pathway WebApp

This document describes the **MySQL database design** and **user flows** for a hospital WebApp that handles:

- Cost centers & cost allocation (step-down / double distribution)
- GL expenses & driver statistics
- Unit cost calculation per service
- Tariff setting
- Clinical pathway design
- Patient case tracking & variance analysis

It is written so that an AI coding assistant (AgentAI / cursorAI) can generate a full web application (backend + frontend) from it.

---

## 1. System Overview

The WebApp will allow a hospital to:

- Define cost centers, expense categories, allocation drivers, tariff classes, and cost references (chargemaster).
- Import or input GL expenses and driver statistics.
- Run a cost allocation engine (step-down method).
- Compute unit cost (HPP) per service/procedure.
- Define and publish official tariffs for services.
- Create clinical pathways linked to services and costs.
- Track real patient cases, compare actual vs planned costs, and calculate variance.

**Tech assumptions (flexible for the AI):**

- **DB**: MySQL 8+, InnoDB, `utf8mb4`.
- **Backend**: Any (Laravel / NestJS / Express / Spring / etc.) that maps to the schema.
- **Frontend**: Any (React / Vue / Next / Nuxt / etc.).

---

## 2. Database Conventions

- All tables use:

  ```sql
  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  ```

- `id` column: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` unless stated otherwise.
- Standard timestamps:

  ```sql
  created_at DATETIME NULL,
  updated_at DATETIME NULL
  ```

- `hospital_id` is present on almost all operational tables for multi-hospital tenancy.

The following sections describe the tables in detail.

---

## 3. Database Design (Detailed Tables)

### 3.1. Core & Auth Tables

#### 3.1.1. `hospitals`

**Purpose**: Master of hospitals (multi-tenant).

| Column                    | Type              | Notes                                   |
|---------------------------|-------------------|-----------------------------------------|
| id                        | BIGINT PK         |                                         |
| name                      | VARCHAR(150)      | Hospital name                           |
| code                      | VARCHAR(50)       | Short code, e.g. `RSSS`                 |
| logo_path                 | VARCHAR(255) NULL | Logo URL/path                           |
| theme_color               | VARCHAR(20) NULL  | e.g. `#0099ff`                          |
| address                   | TEXT NULL         |                                         |
| contact                   | VARCHAR(255) NULL |                                          |
| is_active                 | TINYINT(1)        | 1 = active                              |
| financial_year_start_date | DATE NULL         | Start of financial year                 |
| currency_code             | VARCHAR(10)       | e.g. `IDR`                              |
| created_at                | DATETIME NULL     |                                         |
| updated_at                | DATETIME NULL     |                                         |

---

#### 3.1.2. `users`

**Purpose**: Application users (admin, finance, doctor, etc.).

| Column                    | Type              | Notes                                  |
|---------------------------|-------------------|----------------------------------------|
| id                        | BIGINT PK         |                                        |
| name                      | VARCHAR(150)      |                                        |
| email                     | VARCHAR(150)      | UNIQUE                                 |
| email_verified_at         | DATETIME NULL     |                                        |
| password                  | VARCHAR(255)      | Password hash                          |
| remember_token            | VARCHAR(100) NULL |                                        |
| two_factor_secret         | TEXT NULL         | Optional                               |
| two_factor_recovery_codes | TEXT NULL         | Optional                               |
| two_factor_confirmed_at   | DATETIME NULL     |                                        |
| hospital_id               | BIGINT FK         | → hospitals.id                         |
| is_active                 | TINYINT(1)        |                                        |
| created_at                | DATETIME NULL     |                                        |
| updated_at                | DATETIME NULL     |                                        |

Indexes:

- `UNIQUE (email)`  
- `INDEX (hospital_id)`

---

#### 3.1.3. `audit_logs`

**Purpose**: Audit trail of actions in the system.

| Column     | Type          | Notes                          |
|------------|---------------|--------------------------------|
| id         | BIGINT PK     |                                |
| user_id    | BIGINT FK     | → users.id                     |
| action     | VARCHAR(100)  | e.g. `CREATE`, `UPDATE`, `DELETE` |
| model      | VARCHAR(150)  | Table or model name            |
| model_id   | VARCHAR(100)  | Record ID                      |
| changes    | JSON NULL     | Before/after snapshot          |
| created_at | DATETIME NULL |                                |

---

#### 3.1.4. `personal_access_tokens`

**Purpose**: Token-based authentication for APIs or long sessions.

(Implementation can follow Laravel Sanctum style; details omitted as not directly impacting costing/pathway.)

---

### 3.2. Master Data for Costing

#### 3.2.1. `cost_centers`

**Purpose**: Master cost centers (units).

| Column      | Type                       | Notes                            |
|-------------|----------------------------|----------------------------------|
| id          | BIGINT PK                  |                                  |
| hospital_id | BIGINT FK                  | → hospitals.id                   |
| code        | VARCHAR(50)                | e.g. `IGD`, `LAB`, `ADM`        |
| name        | VARCHAR(150)               | Unit full name                   |
| type        | ENUM('support','revenue')  | Support vs revenue-generating    |
| parent_id   | BIGINT NULL FK             | Self-reference → cost_centers.id |
| is_active   | TINYINT(1)                 |                                  |
| created_at  | DATETIME NULL              |                                  |
| updated_at  | DATETIME NULL              |                                  |

Index: `(hospital_id, code)`

---

#### 3.2.2. `expense_categories`

**Purpose**: Expense categories (COA accounts) used for GL.

| Column             | Type                                                  | Notes                       |
|--------------------|-------------------------------------------------------|-----------------------------|
| id                 | BIGINT PK                                             |                             |
| hospital_id        | BIGINT FK                                             | → hospitals.id              |
| account_code       | VARCHAR(50)                                           | COA code                    |
| account_name       | VARCHAR(150)                                          |                             |
| cost_type          | ENUM('fixed','variable','semi_variable')              |                             |
| allocation_category| ENUM('gaji','bhp_medis','bhp_non_medis','depresiasi','lain_lain') | For reporting/grouping |
| is_active          | TINYINT(1)                                            |                             |
| created_at         | DATETIME NULL                                         |                             |
| updated_at         | DATETIME NULL                                         |                             |

---

#### 3.2.3. `allocation_drivers`

**Purpose**: Allocation basis such as floor area, headcount, laundry kg, etc.

| Column           | Type          | Notes                       |
|------------------|---------------|-----------------------------|
| id               | BIGINT PK     |                             |
| hospital_id      | BIGINT FK     | → hospitals.id              |
| name             | VARCHAR(150)  | e.g. `Luas Lantai`          |
| unit_measurement | VARCHAR(50)   | e.g. `m2`, `orang`, `kg`    |
| description      | TEXT NULL     |                             |
| created_at       | DATETIME NULL |                             |
| updated_at       | DATETIME NULL |                             |

---

#### 3.2.4. `tariff_classes`

**Purpose**: Tariff classes for services/rooms.

| Column      | Type          | Notes                      |
|-------------|---------------|----------------------------|
| id          | BIGINT PK     |                            |
| hospital_id | BIGINT FK     | → hospitals.id             |
| code        | VARCHAR(50)   | e.g. `KLS1`, `KLS3`        |
| name        | VARCHAR(150)  | e.g. `Kelas 1`             |
| description | TEXT NULL     |                            |
| is_active   | TINYINT(1)    |                            |
| created_at  | DATETIME NULL |                            |
| updated_at  | DATETIME NULL |                            |

---

#### 3.2.5. `cost_references`

**Purpose**: Catalog of services/items (chargemaster) with costing metadata.

| Column              | Type            | Notes                                   |
|---------------------|-----------------|-----------------------------------------|
| id                  | BIGINT PK       |                                         |
| service_code        | VARCHAR(100)    | Service/procedure code                  |
| simrs_kode_brng     | VARCHAR(100)    | Item code in HIS/SIMRS                  |
| service_description | VARCHAR(255)    |                                         |
| standard_cost       | DECIMAL(15,2)   | Standard/estimated cost                 |
| purchase_price      | DECIMAL(15,2)   | Last purchase price                     |
| selling_price_unit  | DECIMAL(15,2)   | Unit selling price (if any)             |
| selling_price_total | DECIMAL(15,2)   | Total selling price (for bundles)       |
| unit                | VARCHAR(50)     | e.g. `test`, `kali`, `paket`           |
| source              | VARCHAR(100)    | e.g. `SIMRS`, `MANUAL`                  |
| hospital_id         | BIGINT FK       | → hospitals.id                          |
| is_synced_from_simrs| TINYINT(1)      |                                         |
| created_at          | DATETIME NULL   |                                         |
| updated_at          | DATETIME NULL   |                                         |
| last_synced_at      | DATETIME NULL   |                                         |
| cost_center_id      | BIGINT FK       | → cost_centers.id                       |
| expense_category_id | BIGINT FK       | → expense_categories.id                 |
| is_bundle           | TINYINT(1)      | 1 = bundle/package                      |
| active_from         | DATE NULL       |                                         |
| active_to           | DATE NULL       |                                         |

Index: `(hospital_id, service_code)`

---

#### 3.2.6. `jkn_cbg_codes`

**Purpose**: INA-CBG / case-based grouping codes.

| Column     | Type          | Notes         |
|------------|---------------|---------------|
| id         | BIGINT PK     |               |
| cbg_code   | VARCHAR(50)   | INA-CBG code  |
| description| VARCHAR(255)  |               |
| base_tariff| DECIMAL(15,2) | National base |
| updated_at | DATETIME NULL |               |
| created_at | DATETIME NULL |               |

---

### 3.3. GL & Statistics Tables

#### 3.3.1. `gl_expenses`

**Purpose**: Real expenses per cost center & expense category per period.

| Column              | Type            | Notes                          |
|---------------------|-----------------|--------------------------------|
| id                  | BIGINT PK       |                                |
| hospital_id         | BIGINT FK       | → hospitals.id                 |
| period_month        | TINYINT         | 1–12                           |
| period_year         | SMALLINT        | e.g. 2025                      |
| cost_center_id      | BIGINT FK       | → cost_centers.id              |
| expense_category_id | BIGINT FK       | → expense_categories.id        |
| amount              | DECIMAL(18,2)   |                                |
| created_at          | DATETIME NULL   |                                |
| updated_at          | DATETIME NULL   |                                |

Index: `(hospital_id, period_year, period_month, cost_center_id)`

---

#### 3.3.2. `driver_statistics`

**Purpose**: Driver values (e.g. m2, FTE) per cost center & period.

| Column              | Type          | Notes                            |
|---------------------|---------------|----------------------------------|
| id                  | BIGINT PK     |                                  |
| hospital_id         | BIGINT FK     |                                  |
| period_month        | TINYINT       |                                  |
| period_year         | SMALLINT      |                                  |
| cost_center_id      | BIGINT FK     | → cost_centers.id                |
| allocation_driver_id| BIGINT FK     | → allocation_drivers.id          |
| value               | DECIMAL(18,4) | Driver value                     |
| created_at          | DATETIME NULL |                                  |
| updated_at          | DATETIME NULL |                                  |

---

#### 3.3.3. `service_volumes`

**Purpose**: Volume of services per cost reference & tariff class per period.

| Column           | Type            | Notes                              |
|------------------|-----------------|------------------------------------|
| id               | BIGINT PK       |                                    |
| hospital_id      | BIGINT FK       |                                    |
| period_month     | TINYINT         |                                    |
| period_year      | SMALLINT        |                                    |
| cost_reference_id| BIGINT FK       | → cost_references.id               |
| tariff_class_id  | BIGINT FK NULL  | → tariff_classes.id (nullable)     |
| total_quantity   | DECIMAL(18,2)   |                                    |
| created_at       | DATETIME NULL   |                                    |
| updated_at       | DATETIME NULL   |                                    |

---

### 3.4. Cost Allocation Tables

#### 3.4.1. `allocation_maps`

**Purpose**: Defines which support cost centers allocate to others and using what driver.

| Column               | Type          | Notes                         |
|----------------------|---------------|-------------------------------|
| id                   | BIGINT PK     |                               |
| hospital_id          | BIGINT FK     |                               |
| source_cost_center_id| BIGINT FK     | → cost_centers.id             |
| allocation_driver_id | BIGINT FK     | → allocation_drivers.id       |
| step_sequence        | INT           | Order of step-down allocation |
| created_at           | DATETIME NULL |
| updated_at           | DATETIME NULL |

---

#### 3.4.2. `allocation_results`

**Purpose**: Stores allocation results (how much cost moved from source to target).

| Column               | Type            | Notes                           |
|----------------------|-----------------|---------------------------------|
| id                   | BIGINT PK       |                                 |
| hospital_id          | BIGINT FK       |                                 |
| period_month         | TINYINT         |                                 |
| period_year          | SMALLINT        |                                 |
| source_cost_center_id| BIGINT FK       | → cost_centers.id               |
| target_cost_center_id| BIGINT FK       | → cost_centers.id               |
| allocation_step      | VARCHAR(50)     | e.g. `direct`, `step_1`, etc.   |
| allocated_amount     | DECIMAL(18,2)   |                                 |
| created_at           | DATETIME NULL   |                                 |
| updated_at           | DATETIME NULL   |                                 |

---

### 3.5. Unit Cost & Tariff Tables

#### 3.5.1. `unit_cost_calculations`

**Purpose**: Result of unit cost calculation per service per period.

| Column               | Type            | Notes                          |
|----------------------|-----------------|--------------------------------|
| id                   | BIGINT PK       |                                |
| hospital_id          | BIGINT FK       |                                |
| period_year          | SMALLINT        |                                |
| period_month         | TINYINT NULL    | Null if yearly only            |
| cost_reference_id    | BIGINT FK       | → cost_references.id           |
| direct_cost_material | DECIMAL(18,2)   |                                |
| direct_cost_labor    | DECIMAL(18,2)   |                                |
| indirect_cost_overhead| DECIMAL(18,2)  |                                |
| total_unit_cost      | DECIMAL(18,2)   |                                |
| version_label        | VARCHAR(100)    | e.g. `UC_2025_JAN`             |
| created_at           | DATETIME NULL   |                                |
| updated_at           | DATETIME NULL   |                                |

---

#### 3.5.2. `final_tariffs`

**Purpose**: Final tariff structure per service & class, linked to SK/approval.

| Column                  | Type            | Notes                            |
|-------------------------|-----------------|----------------------------------|
| id                      | BIGINT PK       |                                  |
| hospital_id             | BIGINT FK       |                                  |
| cost_reference_id       | BIGINT FK       | → cost_references.id             |
| tariff_class_id         | BIGINT FK NULL  | → tariff_classes.id              |
| unit_cost_calculation_id| BIGINT FK       | → unit_cost_calculations.id      |
| sk_number               | VARCHAR(100)    | Official decree number           |
| base_unit_cost          | DECIMAL(18,2)   | Copied from unit cost            |
| margin_percentage       | DECIMAL(5,4)    | 0.2 = 20%                        |
| jasa_sarana             | DECIMAL(18,2)   | Facility component               |
| jasa_pelayanan          | DECIMAL(18,2)   | Professional component           |
| final_tariff_price      | DECIMAL(18,2)   | Final price to patients/payer    |
| effective_date          | DATE            |                                  |
| expired_date            | DATE NULL       |                                  |
| created_at              | DATETIME NULL   |                                  |
| updated_at              | DATETIME NULL   |                                  |

---

### 3.6. Clinical Pathway & Patient Case Tables

#### 3.6.1. `clinical_pathways`

**Purpose**: Clinical pathway per diagnosis / INA-CBG.

| Column            | Type                                      | Notes                    |
|-------------------|-------------------------------------------|--------------------------|
| id                | BIGINT PK                                 |                          |
| name              | VARCHAR(255)                              |                          |
| description       | TEXT NULL                                 |                          |
| diagnosis_code    | VARCHAR(50)                               | ICD-10 code              |
| version           | VARCHAR(50)                               | e.g. `1.0`, `2.0`        |
| effective_date    | DATE                                      |                          |
| created_by        | BIGINT FK                                 | → users.id               |
| status            | ENUM('draft','review','approved','archived') | Workflow status      |
| hospital_id       | BIGINT FK                                 |                          |
| created_at        | DATETIME NULL                             |                          |
| updated_at        | DATETIME NULL                             |                          |
| ina_cbg_code      | VARCHAR(50) NULL                          | Link to `jkn_cbg_codes`  |
| expected_los_days | INT NULL                                  | Expected length of stay  |

---

#### 3.6.2. `pathway_steps`

**Purpose**: Steps in a clinical pathway (actions, labs, meds, etc.).

| Column            | Type            | Notes                                  |
|-------------------|-----------------|----------------------------------------|
| id                | BIGINT PK       |                                        |
| clinical_pathway_id| BIGINT FK      | → clinical_pathways.id                |
| step_order        | INT             | Medical order                          |
| display_order     | INT             | UI order                               |
| category          | VARCHAR(100)    | e.g. `Lab`, `Radiologi`, `Obat`       |
| description       | VARCHAR(255)    |                                        |
| service_code      | VARCHAR(100)    | Map to cost_references.service_code    |
| criteria          | VARCHAR(255) NULL| Clinical criteria (optional)         |
| estimated_cost    | DECIMAL(18,2)   | From unit cost or estimated           |
| quantity          | DECIMAL(10,2)   | Number of times                       |
| cost_reference_id | BIGINT FK NULL  | → cost_references.id                  |
| hospital_id       | BIGINT FK       |                                        |
| created_at        | DATETIME NULL   |                                        |
| updated_at        | DATETIME NULL   |                                        |
| cost_center_id    | BIGINT FK NULL  | → cost_centers.id                     |
| is_mandatory      | TINYINT(1)      | 1 = mandatory step                    |

---

#### 3.6.3. `patient_cases`

**Purpose**: Real patients’ cases that may follow a clinical pathway.

| Column                  | Type            | Notes                              |
|-------------------------|-----------------|------------------------------------|
| id                      | BIGINT PK       |                                    |
| patient_id              | BIGINT          | External patient reference (HIS)   |
| medical_record_number   | VARCHAR(100)    | MRN                                |
| clinical_pathway_id     | BIGINT FK NULL  | → clinical_pathways.id            |
| admission_date          | DATETIME        |                                    |
| discharge_date          | DATETIME NULL   |                                    |
| primary_diagnosis       | VARCHAR(50)     | ICD-10                             |
| ina_cbg_code            | VARCHAR(50) NULL|                                    |
| actual_total_cost       | DECIMAL(18,2)   | Sum of actual costs                |
| ina_cbg_tariff          | DECIMAL(18,2)   | Tariff received from INA-CBG       |
| additional_diagnoses    | TEXT NULL       |                                    |
| compliance_percentage   | DECIMAL(5,2)    | % compliance with pathway          |
| cost_variance           | DECIMAL(18,2)   | Actual cost - Estimated cost       |
| input_by                | BIGINT FK       | → users.id                         |
| input_date              | DATETIME        |                                    |
| hospital_id             | BIGINT FK       |                                    |
| created_at              | DATETIME NULL   |                                    |
| updated_at              | DATETIME NULL   |                                    |
| calculated_total_tariff | DECIMAL(18,2)   | Internal billing total             |
| reimbursement_scheme    | VARCHAR(50)     | e.g. `JKN`, `Umum`, `Asuransi`     |
| unit_cost_version       | VARCHAR(100)    | e.g. `UC_2025_JAN`                 |

---

#### 3.6.4. `case_details`

**Purpose**: Detailed actions/services performed within a patient case.

| Column            | Type            | Notes                                  |
|-------------------|-----------------|----------------------------------------|
| id                | BIGINT PK       |                                        |
| patient_case_id   | BIGINT FK       | → patient_cases.id                     |
| pathway_step_id   | BIGINT FK NULL  | → pathway_steps.id (optional)          |
| service_item      | VARCHAR(255)    | Human-readable name                    |
| service_code      | VARCHAR(100)    |                                        |
| status            | VARCHAR(50)     | `planned`, `done`, `skipped`           |
| performed         | TINYINT(1)      | 1 = performed                          |
| quantity          | DECIMAL(10,2)   |                                        |
| actual_cost       | DECIMAL(18,2)   | Actual cost used                       |
| service_date      | DATETIME NULL   |                                        |
| hospital_id       | BIGINT FK       |                                        |
| created_at        | DATETIME NULL   |                                        |
| updated_at        | DATETIME NULL   |                                        |
| cost_reference_id | BIGINT FK NULL  | → cost_references.id                   |
| unit_cost_applied | DECIMAL(18,2)   | Unit cost version applied              |
| tariff_applied    | DECIMAL(18,2)   | Tariff per unit applied                |

---

#### 3.6.5. `pathway_tariff_summaries`

**Purpose**: Summary of estimated cost & tariff for each clinical pathway.

| Column                       | Type            | Notes                      |
|------------------------------|-----------------|----------------------------|
| id                           | BIGINT PK       |                            |
| hospital_id                  | BIGINT FK       |                            |
| clinical_pathway_id          | BIGINT FK       | → clinical_pathways.id     |
| unit_cost_calculation_version| VARCHAR(100)    | e.g. `UC_2025_JAN`         |
| estimated_total_cost         | DECIMAL(18,2)   | Sum of estimated costs     |
| estimated_total_tariff       | DECIMAL(18,2)   | Sum of tariffs             |
| created_at                   | DATETIME NULL   |                            |
| updated_at                   | DATETIME NULL   |                            |

---

## 4. User Flows

These flows guide how the frontend routes, backend endpoints, and database interactions should work. An AI agent can map these flows to REST APIs / pages.

### 4.1. Flow: Initial Master Data Setup

**Actors**: Admin, Financial Manager  
**Goal**: Configure base references for costing & tariffs.

1. **Admin login**
   - Reads from `users`.
   - Logs action to `audit_logs`.

2. **Create hospitals (if multi-hospital)**
   - UI: `/admin/hospitals`
   - CRUD → `hospitals`.

3. **Define cost centers**
   - UI: `/master/cost-centers`
   - CRUD → `cost_centers` (with types `support` / `revenue`).

4. **Define expense categories**
   - UI: `/master/expense-categories`
   - CRUD → `expense_categories`.

5. **Define allocation drivers**
   - UI: `/master/allocation-drivers`
   - CRUD → `allocation_drivers`.

6. **Define tariff classes**
   - UI: `/master/tariff-classes`
   - CRUD → `tariff_classes`.

7. **Define cost references (services/items)**
   - UI: `/master/cost-references`
   - CRUD → `cost_references`, with mapping to `cost_centers` & `expense_categories`.
   - Optionally provide import from SIMRS for `simrs_kode_brng`, `purchase_price`, etc.

---

### 4.2. Flow: GL Posting & Driver Statistics

**Actors**: Financial Manager, Costing Analyst  
**Goal**: Input real operational costs and allocation drivers.

1. **Input / Import GL Expenses**
   - UI: `/gl-expenses/import` for CSV/XLSX upload.
   - UI: `/gl-expenses` for manual edit/list.
   - Backend: Insert rows into `gl_expenses` with:
     - `hospital_id`, `period_month`, `period_year`, `cost_center_id`, `expense_category_id`, `amount`.

2. **Input Driver Statistics**
   - UI: `/driver-statistics`
   - Backend: Insert into `driver_statistics`:
     - `hospital_id`, `period_month`, `period_year`, `cost_center_id`, `allocation_driver_id`, `value`.

Validation rules:

- Every `cost_center_id` used must exist in `cost_centers`.
- Every `expense_category_id` used must exist in `expense_categories`.
- Every `allocation_driver_id` used must exist in `allocation_drivers`.

---

### 4.3. Flow: Cost Allocation Setup & Execution

**Actor**: Costing Analyst  
**Goal**: Configure and run step-down allocation.

1. **Define allocation maps**
   - UI: `/allocation/maps`
   - User selects `source_cost_center_id` (support unit), `allocation_driver_id`, and `step_sequence`.
   - Backend: CRUD on `allocation_maps`.

2. **Run allocation**
   - Endpoint: `POST /allocation/run?hospital_id=&month=&year=`
   - Server steps:
     - For each `allocation_maps` entry:
       - Compute total cost of `source_cost_center_id` from `gl_expenses` (+ previously allocated costs if needed).
       - Fetch relevant `driver_statistics` for that `allocation_driver_id` and period.
       - Calculate share per target cost center (based on driver values).
       - Write allocated amounts into `allocation_results`:
         - `source_cost_center_id`, `target_cost_center_id`, `allocated_amount`, `allocation_step`.

3. **Review allocation results**
   - UI: `/allocation/results?month=&year=`
   - Backend: Read from `allocation_results`, group by source/target.

---

### 4.4. Flow: Unit Cost Calculation

**Actor**: Costing Analyst  
**Goal**: Compute unit cost per service/procedure.

1. **Input/import service volumes**
   - UI: `/service-volumes`
   - Backend: Insert into `service_volumes`:
     - `hospital_id`, `period_month`, `period_year`, `cost_reference_id`, `tariff_class_id` (optional), `total_quantity`.

2. **Run unit cost calculation**
   - Endpoint: `POST /unit-cost/calculate?hospital_id=&month=&year=&version_label=`
   - Backend steps (simplified logic):
     - Compute **total cost per cost_center**:
       - Direct cost from `gl_expenses`.
       - Overhead from `allocation_results` (sum of `allocated_amount` where target = cost_center).
     - Map cost centers to services via `cost_references.cost_center_id`.
     - Use `service_volumes` to distribute cost to each `cost_reference_id`:
       - Derive `direct_cost_material`, `direct_cost_labor`, `indirect_cost_overhead` (depending on `expense_categories.allocation_category`).
       - Compute `total_unit_cost = direct_cost_material + direct_cost_labor + indirect_cost_overhead`.
     - Insert into `unit_cost_calculations` with the given `version_label`.

3. **Review unit cost results**
   - UI: `/unit-cost/results?version_label=...`
   - Backend: Query `unit_cost_calculations` joined with `cost_references`.

---

### 4.5. Flow: Tariff Setting

**Actors**: Financial Manager, Tariff Committee, Director  
**Goal**: Set final hospital tariffs per service & class.

1. **Tariff draft/simulation**
   - UI: `/tariffs/draft?version_label=UC_2025_JAN`
   - Backend: Fetch `unit_cost_calculations` and allow the user to try different `margin_percentage` per service or global.
   - This can be done in-memory or in a temporary table (optional).

2. **Save final tariffs**
   - UI: `/tariffs/final/create`
   - Form fields:
     - `cost_reference_id`
     - `tariff_class_id` (optional)
     - `unit_cost_calculation_id`
     - `base_unit_cost`
     - `margin_percentage`
     - `jasa_sarana`
     - `jasa_pelayanan`
     - `sk_number`
     - `effective_date`
   - Backend: Insert into `final_tariffs`.

3. **View tariff list**
   - UI: `/tariffs/final`
   - Backend: Query `final_tariffs` joined with `cost_references` and `tariff_classes`.

---

### 4.6. Flow: Clinical Pathway Creation & Management

**Actors**: Pathway Designer, Medical Committee  
**Goal**: Define and approve clinical pathways linked to cost & tariffs.

1. **Create clinical pathway**
   - UI: `/pathways`
   - Form → insert into `clinical_pathways` with fields:
     - `name`, `description`, `diagnosis_code`, `ina_cbg_code`, `expected_los_days`, `version`, `effective_date`, `created_by`, `status='draft'`, `hospital_id`.

2. **Add pathway steps**
   - UI: `/pathways/{id}/steps`
   - For each step:
     - Choose `service_code` or `cost_reference_id` from `cost_references` (auto-complete).
     - Fill `category`, `description`, `quantity`, `is_mandatory`, `criteria`.
     - Backend auto-fills `estimated_cost` from latest relevant `unit_cost_calculations.total_unit_cost` (if exists) and `cost_center_id` from `cost_references`.
   - Insert rows into `pathway_steps`.

3. **Recalculate pathway summary**
   - Endpoint: `POST /pathways/{id}/recalculate-summary`
   - Backend:
     - Sum `pathway_steps.estimated_cost * quantity` → `estimated_total_cost`.
     - Optionally derive `estimated_total_tariff` using `final_tariffs.final_tariff_price` for each step.
     - Insert/update row in `pathway_tariff_summaries` for that pathway.

4. **Approve pathway**
   - UI: `/pathways/{id}` with an Approve button (only for users with Medical Committee role).
   - Backend: update `clinical_pathways.status = 'approved'`.

---

### 4.7. Flow: Patient Case Recording & Variance Analysis

**Actors**: Case Manager, Medical Records, Costing Analyst  
**Goal**: Record real patient cases and compare with planned pathway.

1. **Register a patient case**
   - UI: `/cases`
   - Form:
     - `patient_id` or `medical_record_number`
     - optional `clinical_pathway_id`
     - `admission_date`, `primary_diagnosis`, `ina_cbg_code`, `reimbursement_scheme`
   - Insert into `patient_cases` with `status` implicitly handled via business logic.

2. **Fill case details (services performed)**
   - UI: `/cases/{id}/details`
   - Options:
     - Generate “planned” rows from `pathway_steps` (template) when a case is linked to a clinical pathway.
     - Mark rows as `performed=1` when done and set `status='done'`.
     - For each service performed:
       - Choose `cost_reference_id` or `service_code`.
       - Auto-fill `unit_cost_applied` from `unit_cost_calculations.total_unit_cost` based on `unit_cost_version` on `patient_cases`.
       - Auto-fill `tariff_applied` from `final_tariffs.final_tariff_price`.
       - Save `actual_cost` if needed (might equal `unit_cost_applied` or be manually adjusted).
   - Insert/Update rows in `case_details`.

3. **Recalculate case totals & variance**
   - Endpoint: `POST /cases/{id}/recalculate`
   - Backend:
     - `actual_total_cost` = SUM(`case_details.actual_cost`).
     - `calculated_total_tariff` = SUM(`case_details.tariff_applied * quantity`).
     - Calculate `compliance_percentage`:
       - (# mandatory `pathway_steps` that have a corresponding `case_details` with `performed=1`) / total mandatory steps.
     - Get `estimated_total_cost` from `pathway_tariff_summaries` (if case linked to a pathway).
     - `cost_variance` = `actual_total_cost - estimated_total_cost` (if applicable).
     - Update row in `patient_cases`.

4. **View case analysis**
   - UI: `/cases/{id}/analysis`
   - Displays:
     - planned vs actual steps,
     - costs and tariffs,
     - compliance,
     - variance.

---

### 4.8. Flow: Reporting & Analytics

**Actors**: Management, Auditor  
**Goal**: Summarize key financial & clinical information.

Example reports:

1. **Cost center cost report (pre-/post-allocation)**
   - Data sources: `gl_expenses`, `allocation_results`, `cost_centers`.

2. **Unit cost per service report**
   - Data sources: `unit_cost_calculations`, `cost_references`, `cost_centers`.

3. **Tariff & margin report**
   - Data sources: `final_tariffs`, `unit_cost_calculations`.

4. **Pathway compliance & cost variance report**
   - Data sources: `patient_cases`, `case_details`, `clinical_pathways`, `pathway_steps`, `pathway_tariff_summaries`.

These can be implemented as REST endpoints or SQL views, and rendered in the frontend as tables/charts.

---

## 5. Implementation Guidance for AgentAI / cursorAI

### 5.1. Suggested Implementation Order

1. **Auth & Core**: `users`, `hospitals`, `audit_logs`, login/logout, role/permission system.
2. **Master Data**: `cost_centers`, `expense_categories`, `allocation_drivers`, `tariff_classes`, `cost_references`, `jkn_cbg_codes`.
3. **GL & Drivers**: `gl_expenses`, `driver_statistics` + UI for import.
4. **Allocation Engine**: `allocation_maps`, `allocation_results` + allocation run endpoint.
5. **Unit Costing**: `service_volumes`, `unit_cost_calculations` + calculation endpoint.
6. **Tariff Setting**: `final_tariffs` + UI for simulation & final tariff entry.
7. **Clinical Pathways**: `clinical_pathways`, `pathway_steps`, `pathway_tariff_summaries`.
8. **Patient Cases**: `patient_cases`, `case_details` + recalculation logic.
9. **Analytics**: Reporting endpoints and dashboards.

### 5.2. ORM & API Hints

- Use an ORM (Eloquent / TypeORM / Prisma / etc.) and map each table 1:1 to an entity/model.
- Implement soft validation on foreign keys (check existence before insert/update).
- For allocation & unit cost calculation, implement services/use-cases rather than putting logic directly in controllers.
- Provide API documentation (OpenAPI/Swagger) so the frontend can easily integrate.

---

This markdown file can be used as a **single source of truth** for an AI coding assistant to generate:

- SQL migrations / schema
- Backend models, controllers, and services
- Frontend pages and forms
- Allocation & costing calculation logic
