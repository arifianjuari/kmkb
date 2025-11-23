# Product Requirements Document (PRD)
## WebApp Costing, Tariff, and Clinical Pathway Management System

### 1. Overview
A comprehensive web application for hospitals to manage costing, tariff setting, clinical pathways, and patient case monitoring. This system integrates financial, operational, and clinical components into a unified digital platform.

### 2. Goals
- Standardize management of cost centers, expenses, and cost allocation.
- Provide transparent and auditable unit cost and tariff calculations.
- Integrate costing with clinical pathway design for evidence-based care.
- Enable monitoring of actual patient care costs vs pathway standards.
- Improve decision support for hospital management and medical committee.

### 3. Key Modules
1. **Dashboard**
2. **Master Data**
3. **GL & Expense Management**
4. **Cost Allocation Engine**
5. **Unit Costing**
6. **Tariff Setting**
7. **Clinical Pathway Management**
8. **Patient Case Management**
9. **Analytics & Reporting**
10. **System Administration**

### 4. User Roles & Permissions
- **Admin** – Full access, system settings, user management.
- **Financial Manager** – GL management, tariff approval.
- **Costing Analyst** – Allocation, unit cost, tariff simulation.
- **Medical Committee** – Clinical pathway review & approval.
- **Pathway Designer** – Create/edit clinical pathways.
- **Case Manager** – Input patient cases and actual costs.
- **Auditor** – Read-only access to logs & financial outputs.

### 5. Functional Requirements

#### 5.1 Dashboard
- Display KPIs: total cost, unit cost trends, tariff summaries.
- Show pathway compliance performance.
- Highlight cost overruns & anomalies.

#### 5.2 Master Data
Users can manage:
- Hospitals
- Cost Centers
- Expense Categories
- Allocation Drivers
- Tariff Classes
- Cost References (items, services, SIMRS-linked)
- JKN CBG Codes

CRUD operations, bulk import, validation rules.

#### 5.3 GL & Expenses
- Input monthly/annual GL entries.
- Mapping cost center & expense category.
- Import GL from accounting systems (CSV/XLSX).
- Validate completeness and missing cost categories.

#### 5.4 Cost Allocation Engine
- Setup allocation maps.
- Step-down method: allocation sequencing.
- Automatic calculation of allocation shares using driver statistics.
- Store allocation results.
- Re-run allocation for different versions.

#### 5.5 Unit Costing
- Import service volume.
- Compute direct, indirect, and overhead costs per service.
- Store results as a versioned unit cost dataset.
- Provide audit trail: breakdown by cost center and category.

#### 5.6 Tariff Management
- Draft tariff: markup simulation.
- Final tariff: based on SK approval.
- Store final tariffs and associate with classes.
- Track effective and expired dates.

#### 5.7 Clinical Pathway
- Create pathway headers.
- Add/edit pathway steps.
- Link steps to cost references.
- Auto-fill estimated unit cost.
- Produce pathway total estimated cost & tariff comparison.
- Approval workflow.

#### 5.8 Patient Case Management
- Register patient case linked to a pathway.
- Input service details performed.
- Auto-compare planned vs actual actions.
- Detect deviations and compliance percentage.
- Calculate variance: actual cost vs estimated pathway cost.

#### 5.9 Analytics & Reporting
Reports:
- Cost Center Performance
- Allocation Results Summary
- Unit Cost Summary by Department
- Tariff Comparison (Internal vs INA-CBG)
- Pathway Compliance
- Case Variance Analysis

Export: PDF, Excel, CSV.

#### 5.10 System Administration
- User management, roles, access control.
- Audit logs (CRUD actions).
- Token management for API integrations.

### 6. Nonfunctional Requirements
- **Security**: Role-based access, audit trails.
- **Performance**: Must handle large GL datasets.
- **Availability**: >99% uptime.
- **Scalability**: Support multi-hospital instances.
- **API-ready**: For SIMRS integration.
- **Auditability**: All financial data must have traceability.

### 7. Data Model Overview
Includes these key tables:
- cost_centers
- expense_categories
- gl_expenses
- driver_statistics
- allocation_maps
- allocation_results
- service_volumes
- unit_cost_calculations
- final_tariffs
- clinical_pathways
- pathway_steps
- patient_cases
- case_details
- pathway_tariff_summaries

Relationships follow the ERD previously created.

### 8. Success Metrics
- Reduction of manual costing workload by 70%.
- Consistent unit cost calculation monthly.
- Increased accuracy of tariff decisions.
- Improved clinical pathway compliance by >20%.
- Alignment with INA-CBG benchmarking KPIs.

