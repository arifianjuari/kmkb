# Rancangan Struktur Menu & Submenu

## WebApp Costing, Tariff, and Clinical Pathway Management System

Dokumen ini merancang struktur menu dan submenu yang sistematis berdasarkan PRD, SPEC, dan workflow aplikasi.

---

## 1. Struktur Menu Utama

### 1.1 Dashboard

**Icon:** 📊  
**Route:** `/dashboard`  
**Akses:** Semua role (kecuali Superadmin melihat dashboard berbeda)  
**Deskripsi:** Ringkasan eksekutif dan KPI utama

**Submenu:** Tidak ada (single page)

**Widget/Konten:**

- Total Cost Overview
- Unit Cost Trends
- Tariff Summary
- Pathway Compliance Performance
- Cost Overruns & Anomalies
- Recent Activities

---

### 1.2 Master Data

**Icon:** 📋  
**Route:** `/master-data` (parent route, bisa dropdown)  
**Akses:** Admin, Financial Manager, Costing Analyst  
**Deskripsi:** Pengelolaan data master untuk costing dan tariff

**Submenu:**

#### 1.2.1 Cost Centers

- **Route:** `/master-data/cost-centers`
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Fitur:**
  - List Cost Centers
  - Create/Edit Cost Center
  - Set Parent-Child Relationship
  - Set Type (Support/Revenue)
  - Import/Export

#### 1.2.2 Expense Categories

- **Route:** `/master-data/expense-categories`
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Fitur:**
  - List Expense Categories
  - Create/Edit Expense Category
  - Set Cost Type (Fixed/Variable/Semi-Variable)
  - Set Allocation Category
  - Import/Export

#### 1.2.3 Allocation Drivers

- **Route:** `/master-data/allocation-drivers`
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Fitur:**
  - List Allocation Drivers
  - Create/Edit Allocation Driver
  - Set Unit Measurement
  - Import/Export

#### 1.2.4 Tariff Classes

- **Route:** `/master-data/tariff-classes`
- **Akses:** Admin, Financial Manager
- **Fitur:**
  - List Tariff Classes
  - Create/Edit Tariff Class
  - Set Active/Inactive Status
  - Import/Export

#### 1.2.5 Cost References

- **Route:** `/cost-references` (sudah ada)
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Fitur:**
  - List Cost References
  - Create/Edit Cost Reference
  - Link to Cost Center & Expense Category
  - SIMRS Sync
  - Import/Export
  - Bulk Operations

#### 1.2.6 JKN CBG Codes

- **Route:** `/jkn-cbg-codes` (sudah ada)
- **Akses:** Semua role (read), Admin (CRUD)
- **Fitur:**
  - List JKN CBG Codes
  - Create/Edit JKN CBG Code
  - View Base Tariff
  - Import/Export

---

### 1.3 GL & Expense Management

**Icon:** 💰  
**Route:** `/gl-expenses` (parent route)  
**Akses:** Financial Manager, Costing Analyst  
**Deskripsi:** Input dan pengelolaan General Ledger expenses

**Submenu:**

#### 1.3.1 GL Expenses

- **Route:** `/gl-expenses`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List GL Expenses (filter by period, cost center, category)
  - Create/Edit GL Expense Entry
  - Import from CSV/XLSX
  - Validate Completeness
  - Missing Cost Categories Report
  - Export to Excel

#### 1.3.2 Driver Statistics

- **Route:** `/gl-expenses/driver-statistics`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List Driver Statistics (filter by period, cost center, driver)
  - Create/Edit Driver Statistics
  - Bulk Input by Period
  - Import from CSV/XLSX
  - Export to Excel

#### 1.3.3 Service Volumes

- **Route:** `/gl-expenses/service-volumes`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List Service Volumes (filter by period, service, tariff class)
  - Create/Edit Service Volume
  - Bulk Input by Period
  - Import from CSV/XLSX
  - Export to Excel

---

### 1.4 Cost Allocation

**Icon:** 🔄  
**Route:** `/allocation` (parent route)  
**Akses:** Costing Analyst, Financial Manager  
**Deskripsi:** Setup dan eksekusi cost allocation engine

**Submenu:**

#### 1.4.1 Allocation Maps

- **Route:** `/allocation/maps`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - List Allocation Maps
  - Create/Edit Allocation Map
  - Set Source Cost Center
  - Set Allocation Driver
  - Set Step Sequence
  - Visual Flow Diagram

#### 1.4.2 Run Allocation

- **Route:** `/allocation/run`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Select Period (Month/Year)
  - Preview Allocation Configuration
  - Run Allocation Engine
  - View Progress
  - Version Management

#### 1.4.3 Allocation Results

- **Route:** `/allocation/results`
- **Akses:** Costing Analyst, Financial Manager, Admin
- **Fitur:**
  - List Allocation Results (filter by period, version)
  - View by Source/Target Cost Center
  - View by Allocation Step
  - Summary Report
  - Export to Excel/PDF
  - Compare Versions

---

### 1.5 Unit Costing

**Icon:** 📊  
**Route:** `/unit-cost` (parent route)  
**Akses:** Costing Analyst, Financial Manager  
**Deskripsi:** Perhitungan unit cost per service

**Submenu:**

#### 1.5.1 Service Volumes

- **Route:** `/unit-cost/service-volumes`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Link ke `/gl-expenses/service-volumes` atau duplikat
  - View Service Volumes by Period
  - Validate Volume Data

#### 1.5.2 Calculate Unit Cost

- **Route:** `/unit-cost/calculate`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Select Period (Month/Year)
  - Select Version Label
  - Preview Configuration
  - Run Unit Cost Calculation
  - View Progress
  - Version Management

#### 1.5.3 Unit Cost Results

- **Route:** `/unit-cost/results`
- **Akses:** Costing Analyst, Financial Manager, Admin
- **Fitur:**
  - List Unit Cost Results (filter by version, period, service)
  - View Breakdown (Direct Material, Direct Labor, Overhead)
  - Audit Trail by Cost Center
  - Compare Versions
  - Export to Excel/PDF

---

### 1.6 Tariff Management

**Icon:** 💵  
**Route:** `/tariffs` (parent route)  
**Akses:** Financial Manager, Admin  
**Deskripsi:** Pengaturan dan pengelolaan tariff

**Submenu:**

#### 1.6.1 Tariff Simulation

- **Route:** `/tariffs/simulation`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - Select Unit Cost Version
  - Set Margin Percentage (global or per service)
  - Preview Tariff Calculation
  - Compare Scenarios
  - Export Simulation Results

#### 1.6.2 Final Tariffs

- **Route:** `/tariffs/final`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - List Final Tariffs (filter by service, class, effective date)
  - Create/Edit Final Tariff
  - Link to Unit Cost Calculation
  - Set SK Number
  - Set Effective/Expired Date
  - Approval Workflow
  - Export to Excel/PDF

#### 1.6.3 Tariff Explorer

- **Route:** `/tariffs/explorer`
- **Akses:** Semua role (read)
- **Fitur:**
  - Search Tariffs by Service/Code
  - Filter by Tariff Class
  - View Tariff History
  - Compare Internal vs INA-CBG
  - Export to Excel

---

### 1.7 Clinical Pathways

**Icon:** 🏥  
**Route:** `/pathways` (sudah ada)  
**Akses:** Pathway Designer, Medical Committee, Admin  
**Deskripsi:** Pengelolaan clinical pathways

**Submenu:**

#### 1.7.1 Pathway List

- **Route:** `/pathways` (sudah ada)
- **Akses:** Semua role (read), Pathway Designer (CRUD)
- **Fitur:**
  - List Pathways (filter by status, diagnosis, version)
  - Create/Edit Pathway Header
  - Duplicate Pathway
  - Version Management
  - Status Filter (Draft/Review/Approved/Archived)

#### 1.7.2 Pathway Builder

- **Route:** `/pathways/{id}/builder` (sudah ada)
- **Akses:** Pathway Designer, Medical Committee
- **Fitur:**
  - Visual Pathway Builder
  - Add/Edit/Delete Steps
  - Link Steps to Cost References
  - Auto-fill Estimated Cost
  - Set Step Order
  - Set Mandatory/Optional
  - Import Steps from Template

#### 1.7.3 Pathway Summary

- **Route:** `/pathways/{id}/summary`
- **Akses:** Semua role (read)
- **Fitur:**
  - View Estimated Total Cost
  - View Estimated Total Tariff
  - Breakdown by Step
  - Compare with Unit Cost Version
  - Export to PDF/DOCX

#### 1.7.4 Pathway Approval

- **Route:** `/pathways/{id}/approval`
- **Akses:** Medical Committee, Admin
- **Fitur:**
  - Review Pathway
  - Approve/Reject Pathway
  - Add Comments
  - Approval History

---

### 1.8 Patient Cases

**Icon:** 👤  
**Route:** `/cases` (sudah ada)  
**Akses:** Case Manager, Medical Records, Costing Analyst  
**Deskripsi:** Pengelolaan patient cases dan tracking

**Submenu:**

#### 1.8.1 Case List

- **Route:** `/cases` (sudah ada)
- **Akses:** Semua role (read), Case Manager (CRUD)
- **Fitur:**
  - List Patient Cases (filter by pathway, diagnosis, period)
  - Create New Case
  - Link to Clinical Pathway
  - Upload Cases from Excel
  - Export to Excel

#### 1.8.2 Case Details

- **Route:** `/cases/{id}/details` (sudah ada)
- **Akses:** Case Manager, Medical Records
- **Fitur:**
  - View Case Information
  - Add/Edit Case Details (services performed)
  - Generate Planned Steps from Pathway
  - Mark Services as Performed
  - Auto-fill Unit Cost & Tariff
  - Track Service Dates

#### 1.8.3 Case Analysis

- **Route:** `/cases/{id}/analysis` (sudah ada)
- **Akses:** Semua role (read)
- **Fitur:**
  - View Planned vs Actual Steps
  - View Cost Comparison
  - View Compliance Percentage
  - View Cost Variance
  - View Tariff Comparison
  - Export Analysis Report

---

### 1.9 Reports & Analytics

**Icon:** 📈  
**Route:** `/reports` (sudah ada)  
**Akses:** Semua role (berbeda per report)  
**Deskripsi:** Laporan dan analitik

**Submenu:**

#### 1.9.1 Cost Center Performance

- **Route:** `/reports/cost-center-performance`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - Pre/Post Allocation Cost Report
  - Cost by Expense Category
  - Trend Analysis
  - Export to Excel/PDF

#### 1.9.2 Allocation Results Summary

- **Route:** `/reports/allocation-summary`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Allocation Summary by Period
  - Allocation Flow Diagram
  - Compare Versions
  - Export to Excel/PDF

#### 1.9.3 Unit Cost Summary

- **Route:** `/reports/unit-cost-summary`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Unit Cost by Department
  - Unit Cost Trend
  - Breakdown Analysis
  - Export to Excel/PDF

#### 1.9.4 Tariff Comparison

- **Route:** `/reports/tariff-comparison`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - Internal vs INA-CBG Comparison
  - Tariff by Class
  - Margin Analysis
  - Export to Excel/PDF

#### 1.9.5 Pathway Compliance

- **Route:** `/reports/compliance` (sudah ada)
- **Akses:** Semua role
- **Fitur:**
  - Compliance by Pathway
  - Compliance Trend
  - Compliance by Department
  - Export to Excel/PDF

#### 1.9.6 Case Variance Analysis

- **Route:** `/reports/cost-variance` (sudah ada)
- **Akses:** Semua role
- **Fitur:**
  - Cost Variance by Pathway
  - Cost Variance Trend
  - Variance by Department
  - Export to Excel/PDF

#### 1.9.7 Pathway Performance

- **Route:** `/reports/pathway-performance` (sudah ada)
- **Akses:** Semua role
- **Fitur:**
  - Pathway Performance Metrics
  - LOS Analysis
  - Cost Efficiency
  - Export to Excel/PDF

---

### 1.10 System Administration

**Icon:** ⚙️  
**Route:** `/admin` (parent route)  
**Akses:** Admin, Superadmin  
**Deskripsi:** Pengaturan sistem dan administrasi

**Submenu:**

#### 1.10.1 Hospitals (Superadmin only)

- **Route:** `/hospitals` (sudah ada)
- **Akses:** Superadmin
- **Fitur:**
  - List Hospitals
  - Create/Edit Hospital
  - Set Hospital Configuration
  - Hospital Selection

#### 1.10.2 Users

- **Route:** `/users` (sudah ada)
- **Akses:** Admin, Superadmin
- **Fitur:**
  - List Users
  - Create/Edit User
  - Assign Roles
  - Change Password
  - Set Active/Inactive

#### 1.10.3 Roles & Permissions

- **Route:** `/admin/roles`
- **Akses:** Admin, Superadmin
- **Fitur:**
  - List Roles
  - Create/Edit Role
  - Assign Permissions
  - Role Hierarchy

#### 1.10.4 Audit Logs

- **Route:** `/audit-logs` (sudah ada)
- **Akses:** Admin, Auditor
- **Fitur:**
  - List Audit Logs (filter by user, action, model, date)
  - View Log Details
  - Export Logs
  - Clear Old Logs

#### 1.10.5 API Tokens

- **Route:** `/admin/api-tokens`
- **Akses:** Admin
- **Fitur:**
  - List API Tokens
  - Create/Revoke Token
  - Set Token Permissions
  - Token Usage Logs

#### 1.10.6 System Settings

- **Route:** `/admin/settings`
- **Akses:** Admin
- **Fitur:**
  - General Settings
  - Financial Year Settings
  - Currency Settings
  - Integration Settings (SIMRS)
  - Backup Settings

---

### 1.11 SIMRS Integration

**Icon:** 🔌  
**Route:** `/simrs` (parent route, sudah ada)  
**Akses:** Admin, Financial Manager  
**Deskripsi:** Integrasi dengan SIMRS

**Submenu:**

#### 1.11.1 Connection Settings

- **Route:** `/simrs/settings`
- **Akses:** Admin
- **Fitur:**
  - Configure SIMRS Database Connection
  - Test Connection
  - Set Sync Schedule

#### 1.11.2 Data Sources

- **Route:** `/simrs` (sudah ada)
- **Akses:** Admin, Financial Manager
- **Fitur:**
  - Master Barang
  - Tindakan Rawat Jalan
  - Tindakan Rawat Inap
  - Laboratorium
  - Radiologi
  - Operasi
  - Kamar

#### 1.11.3 Sync Management

- **Route:** `/simrs/sync` (sudah ada)
- **Akses:** Admin, Financial Manager
- **Fitur:**
  - Manual Sync
  - Sync History
  - Sync Status
  - Error Logs

---

## 2. Role-Based Menu Visibility

### 2.1 Superadmin

- Dashboard (Superadmin View)
- Hospitals
- Users
- Audit Logs
- System Settings

### 2.2 Admin

- Dashboard
- Master Data (semua)
- GL & Expense Management
- Cost Allocation
- Unit Costing
- Tariff Management
- Clinical Pathways
- Patient Cases
- Reports & Analytics (semua)
- System Administration (kecuali Hospitals)
- SIMRS Integration

### 2.3 Financial Manager

- Dashboard
- Master Data (Cost Centers, Expense Categories, Allocation Drivers, Tariff Classes, Cost References, JKN CBG Codes)
- GL & Expense Management (semua)
- Cost Allocation (semua)
- Unit Costing (semua)
- Tariff Management (semua)
- Clinical Pathways (read only)
- Patient Cases (read only)
- Reports & Analytics (semua)
- SIMRS Integration (read)

### 2.4 Costing Analyst

- Dashboard
- Master Data (Cost Centers, Expense Categories, Allocation Drivers, Cost References, JKN CBG Codes)
- GL & Expense Management (semua)
- Cost Allocation (semua)
- Unit Costing (semua)
- Tariff Management (Simulation only)
- Clinical Pathways (read only)
- Patient Cases (read only)
- Reports & Analytics (Cost Center, Allocation, Unit Cost, Compliance, Variance)

### 2.5 Medical Committee

- Dashboard
- Master Data (JKN CBG Codes - read only)
- Clinical Pathways (read, approve)
- Patient Cases (read only)
- Reports & Analytics (Compliance, Variance, Pathway Performance)

### 2.6 Pathway Designer

- Dashboard
- Master Data (Cost References, JKN CBG Codes - read only)
- Clinical Pathways (CRUD)
- Patient Cases (read only)
- Reports & Analytics (Compliance, Variance, Pathway Performance)

### 2.7 Case Manager

- Dashboard
- Master Data (Cost References, JKN CBG Codes - read only)
- Clinical Pathways (read only)
- Patient Cases (CRUD)
- Reports & Analytics (Compliance, Variance - own cases)

### 2.8 Auditor

- Dashboard (read only)
- Reports & Analytics (semua - read only)
- Audit Logs (read only)

---

## 3. Menu Implementation Notes

### 3.1 Navigation Structure

- **Top Navigation Bar:** Menu utama (Dashboard, Master Data, GL & Expenses, dll)
- **Dropdown Menus:** Untuk menu dengan banyak submenu
- **Sidebar (Optional):** Untuk navigasi cepat di halaman tertentu
- **Breadcrumbs:** Untuk navigasi hierarkis

### 3.2 Menu Icons

- Gunakan icon yang konsisten (Font Awesome, Heroicons, atau custom)
- Icon harus merepresentasikan fungsi menu dengan jelas

### 3.3 Active State

- Highlight menu yang sedang aktif
- Highlight parent menu jika submenu aktif

### 3.4 Responsive Design

- Mobile: Hamburger menu dengan collapsible submenu
- Desktop: Horizontal menu bar dengan dropdown
- Tablet: Hybrid approach

### 3.5 Menu Order

Menu diurutkan berdasarkan workflow:

1. Dashboard (overview)
2. Master Data (setup)
3. GL & Expense Management (input data)
4. Cost Allocation (processing)
5. Unit Costing (processing)
6. Tariff Management (output)
7. Clinical Pathways (clinical setup)
8. Patient Cases (operational)
9. Reports & Analytics (analysis)
10. System Administration (admin)
11. SIMRS Integration (integration)

---

## 4. Implementation Priority

### Phase 1 (Sudah Ada)

- ✅ Dashboard
- ✅ Clinical Pathways
- ✅ Patient Cases
- ✅ Reports (Compliance, Variance)
- ✅ Cost References
- ✅ JKN CBG Codes
- ✅ SIMRS Integration (basic)
- ✅ System Administration (Users, Audit Logs)

### Phase 2 (High Priority)

- ⚠️ Master Data (Cost Centers, Expense Categories, Allocation Drivers, Tariff Classes)
- ⚠️ GL & Expense Management
- ⚠️ Cost Allocation Engine
- ⚠️ Unit Costing Engine
- ⚠️ Tariff Management

### Phase 3 (Enhancement)

- ⚠️ Advanced Reports
- ⚠️ Pathway Tariff Summary
- ⚠️ Enhanced SIMRS Integration
- ⚠️ API Token Management
- ⚠️ System Settings

---

## 5. Struktur Menu Revisi (Revised Menu Structure)

Struktur menu yang direvisi dengan pengelompokan berdasarkan workflow dan peningkatan UX.

---

### 5.1 Dashboard

**Icon:** 📊  
**Route:** `/dashboard`  
**Akses:** Semua role (kecuali Superadmin melihat dashboard berbeda)  
**Deskripsi:** Ringkasan eksekutif dan KPI utama

**Submenu:** Tidak ada (single page)

**Widget/Konten:**

- Executive KPIs
- Costing & Tariff Overview
- Clinical Pathway Compliance
- Case Variance Alerts
- Total Cost Overview
- Unit Cost Trends
- Recent Activities

---

### 5.2 Setup

**Icon:** ⚙️  
**Route:** `/setup` (parent route, dropdown menu)  
**Akses:** Admin, Financial Manager, Costing Analyst  
**Deskripsi:** Konfigurasi awal dan pengaturan data master

**Submenu:**

#### 5.2.1 Costing Setup

- **Route:** `/setup/costing`
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Submenu:**
  - **Cost Centers** (`/setup/costing/cost-centers`)
    - List, Create/Edit, Set Parent-Child, Set Type (Support/Revenue), Import/Export
  - **Expense Categories** (`/setup/costing/expense-categories`)
    - List, Create/Edit, Set Cost Type, Set Allocation Category, Import/Export
  - **Allocation Drivers** (`/setup/costing/allocation-drivers`)
    - List, Create/Edit, Set Unit Measurement, Import/Export
  - **Tariff Classes** (`/setup/costing/tariff-classes`)
    - List, Create/Edit, Set Active/Inactive, Import/Export

#### 5.2.2 Service Catalog

- **Route:** `/setup/service-catalog`
- **Akses:** Admin, Financial Manager, Costing Analyst
- **Submenu:**
  - **Service Items** (`/setup/service-catalog/service-items`) - formerly Cost References
    - List, Create/Edit, Link to Cost Center & Expense Category, SIMRS Sync, Import/Export, Bulk Operations
  - **SIMRS-linked Items** (`/setup/service-catalog/simrs-linked`)
    - View SIMRS-linked services, Sync status, Link/Unlink items
  - **Import/Export** (`/setup/service-catalog/import-export`)
    - Bulk import template, Export service catalog, Sync management

#### 5.2.3 JKN / INA-CBG Codes

- **Route:** `/setup/jkn-cbg-codes`
- **Akses:** Semua role (read), Admin (CRUD)
- **Submenu:**
  - **CBG List** (`/setup/jkn-cbg-codes/list`)
    - List JKN CBG Codes, Create/Edit, Search, Import/Export
  - **Base Tariff Reference** (`/setup/jkn-cbg-codes/base-tariff`)
    - View Base Tariff, Compare with Internal Tariff, Tariff History

#### 5.2.4 SIMRS Integration

- **Route:** `/setup/simrs-integration`
- **Akses:** Admin, Financial Manager
- **Submenu:**
  - **Connection Settings** (`/setup/simrs-integration/settings`)
    - Configure SIMRS Database Connection, Test Connection, Set Sync Schedule
  - **Data Sources** (`/setup/simrs-integration/data-sources`)
    - Master Barang, Tindakan Rawat Jalan, Tindakan Rawat Inap, Laboratorium, Radiologi, Operasi, Kamar
  - **Sync Management** (`/setup/simrs-integration/sync`)
    - Manual Sync, Sync History, Sync Status, Error Logs

---

### 5.3 Data Input

**Icon:** 📥  
**Route:** `/data-input` (parent route)  
**Akses:** Financial Manager, Costing Analyst  
**Deskripsi:** Input dan pengelolaan data operasional

**Submenu:**

#### 5.3.1 GL Expenses

- **Route:** `/data-input/gl-expenses`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List GL Expenses (filter by period, cost center, category)
  - Create/Edit GL Expense Entry
  - Import from CSV/XLSX
  - Validate Completeness
  - Missing Cost Categories Report
  - Export to Excel

#### 5.3.2 Driver Statistics

- **Route:** `/data-input/driver-statistics`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List Driver Statistics (filter by period, cost center, driver)
  - Create/Edit Driver Statistics
  - Bulk Input by Period
  - Import from CSV/XLSX
  - Export to Excel

#### 5.3.3 Service Volumes

- **Route:** `/data-input/service-volumes`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - List Service Volumes (filter by period, service, tariff class)
  - Create/Edit Service Volume
  - Bulk Input by Period
  - Import from CSV/XLSX
  - Export to Excel

#### 5.3.4 Import Center

- **Route:** `/data-input/import-center`
- **Akses:** Financial Manager, Costing Analyst
- **Deskripsi:** Pusat import data untuk semua modul data input
- **Fitur:**
  - Quick Import GL Expenses
  - Quick Import Driver Statistics
  - Quick Import Service Volumes
  - Import History & Status
  - Template Download
  - Validation Summary

---

### 5.4 Costing Process

**Icon:** 🔄  
**Route:** `/costing-process` (parent route)  
**Akses:** Costing Analyst, Financial Manager  
**Deskripsi:** Proses costing dari validasi hingga perhitungan unit cost

**Submenu:**

#### 5.4.1 Pre-Allocation Check

- **Route:** `/costing-process/pre-allocation-check`
- **Akses:** Costing Analyst, Financial Manager
- **Submenu:**
  - **GL Completeness** (`/costing-process/pre-allocation-check/gl-completeness`)
    - Check GL data completeness, Missing entries report, Period validation
  - **Driver Completeness** (`/costing-process/pre-allocation-check/driver-completeness`)
    - Check driver statistics completeness, Missing drivers report, Validation summary
  - **Service Volume Completeness** (`/costing-process/pre-allocation-check/service-volume-completeness`)
    - Check service volume completeness, Missing volumes report, Validation summary
  - **Mapping Validation** (`/costing-process/pre-allocation-check/mapping-validation`)
    - Validate cost center mappings, Validate expense category mappings, Validate allocation driver mappings

#### 5.4.2 Allocation Engine

- **Route:** `/costing-process/allocation`
- **Akses:** Costing Analyst, Financial Manager
- **Submenu:**
  - **Allocation Maps** (`/costing-process/allocation/maps`)
    - List Allocation Maps, Create/Edit Allocation Map, Set Source Cost Center, Set Allocation Driver, Set Step Sequence, Visual Flow Diagram
  - **Run Allocation** (`/costing-process/allocation/run`)
    - Select Period (Month/Year), Preview Allocation Configuration, Run Allocation Engine, View Progress, Version Management
  - **Allocation Results** (`/costing-process/allocation/results`)
    - List Allocation Results (filter by period, version), View by Source/Target Cost Center, View by Allocation Step, Summary Report, Export to Excel/PDF, Compare Versions

#### 5.4.3 Unit Cost Engine

- **Route:** `/costing-process/unit-cost`
- **Akses:** Costing Analyst, Financial Manager
- **Submenu:**
  - **Calculate Unit Cost** (`/costing-process/unit-cost/calculate`)
    - Select Period (Month/Year), Select Version Label, Preview Configuration, Run Unit Cost Calculation, View Progress, Version Management
  - **Unit Cost Results** (`/costing-process/unit-cost/results`)
    - List Unit Cost Results (filter by version, period, service), View Breakdown (Direct Material, Direct Labor, Overhead), Audit Trail by Cost Center, Compare Versions, Export to Excel/PDF
  - **Compare Unit Cost Versions** (`/costing-process/unit-cost/compare`)
    - Select multiple versions, Side-by-side comparison, Variance analysis, Export comparison report

---

### 5.5 Tariff Management

**Icon:** 💵  
**Route:** `/tariffs` (parent route)  
**Akses:** Financial Manager, Admin  
**Deskripsi:** Pengaturan dan pengelolaan tariff

**Submenu:**

#### 5.5.1 Tariff Simulation

- **Route:** `/tariffs/simulation`
- **Akses:** Financial Manager, Costing Analyst
- **Fitur:**
  - Select Unit Cost Version
  - Set Margin Percentage (global or per service)
  - Preview Tariff Calculation
  - Compare Scenarios
  - Export Simulation Results

#### 5.5.2 Tariff Structure Setup

- **Route:** `/tariffs/structure`
- **Akses:** Financial Manager, Admin
- **Deskripsi:** Setup struktur tarif (Jasa Sarana, Jasa Pelayanan, dll)
- **Fitur:**
  - Define Tariff Components (Jasa Sarana, Jasa Pelayanan, dll)
  - Set Component Rules
  - Link to Unit Cost
  - Component Templates

#### 5.5.3 Final Tariffs

- **Route:** `/tariffs/final`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - List Final Tariffs (filter by service, class, effective date)
  - Create/Edit Final Tariff
  - Link to Unit Cost Calculation
  - Set SK Number
  - Set Effective/Expired Date
  - Approval Workflow
  - Export to Excel/PDF

#### 5.5.4 Tariff Explorer

- **Route:** `/tariffs/explorer`
- **Akses:** Semua role (read)
- **Fitur:**
  - Search Tariffs by Service/Code
  - Filter by Tariff Class
  - View Tariff History
  - Compare Internal vs INA-CBG
  - Export to Excel

#### 5.5.5 Tariff vs INA-CBG Comparison

- **Route:** `/tariffs/comparison`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - Compare Internal Tariff vs INA-CBG
  - Margin Analysis
  - Gap Analysis Report
  - Export Comparison Report

---

### 5.6 Clinical Pathways

**Icon:** 🏥  
**Route:** `/pathways` (parent route)  
**Akses:** Pathway Designer, Medical Committee, Admin  
**Deskripsi:** Pengelolaan clinical pathways

**Submenu:**

#### 5.6.1 Pathway Repository

- **Route:** `/pathways`
- **Akses:** Semua role (read), Pathway Designer (CRUD)
- **Fitur:**
  - List Pathways (filter by status, diagnosis, version)
  - Create/Edit Pathway Header
  - Duplicate Pathway
  - Version Management
  - Status Filter (Draft/Review/Approved/Archived)

#### 5.6.2 Pathway Builder

- **Route:** `/pathways/{id}/builder`
- **Akses:** Pathway Designer, Medical Committee
- **Fitur:**
  - Visual Pathway Builder
  - Add/Edit/Delete Steps
  - Link Steps to Cost References
  - Auto-fill Estimated Cost
  - Set Step Order
  - Set Mandatory/Optional
  - Import Steps from Template

#### 5.6.3 Pathway Cost Summary

- **Route:** `/pathways/{id}/summary`
- **Akses:** Semua role (read)
- **Fitur:**
  - View Estimated Total Cost
  - View Estimated Total Tariff
  - Breakdown by Step
  - Compare with Unit Cost Version
  - Export to PDF/DOCX

#### 5.6.4 Pathway Approval

- **Route:** `/pathways/{id}/approval`
- **Akses:** Medical Committee, Admin
- **Fitur:**
  - Review Pathway
  - Approve/Reject Pathway
  - Add Comments
  - Approval History

#### 5.6.5 Template Import/Export

- **Route:** `/pathways/templates`
- **Akses:** Pathway Designer, Admin
- **Fitur:**
  - Export Pathway Template
  - Import Pathway Template
  - Template Library
  - Bulk Import Pathways

---

### 5.7 Patient Cases

**Icon:** 👤  
**Route:** `/cases` (parent route)  
**Akses:** Case Manager, Medical Records, Costing Analyst  
**Deskripsi:** Pengelolaan patient cases dan tracking

**Submenu:**

#### 5.7.1 Case Registration

- **Route:** `/cases`
- **Akses:** Semua role (read), Case Manager (CRUD)
- **Fitur:**
  - List Patient Cases (filter by pathway, diagnosis, period)
  - Create New Case
  - Link to Clinical Pathway
  - Upload Cases from Excel
  - Export to Excel

#### 5.7.2 Case Details

- **Route:** `/cases/{id}/details`
- **Akses:** Case Manager, Medical Records
- **Fitur:**
  - View Case Information
  - Add/Edit Case Details (services performed)
  - Generate Planned Steps from Pathway
  - Mark Services as Performed
  - Auto-fill Unit Cost & Tariff
  - Track Service Dates

#### 5.7.3 Case Costing

- **Route:** `/cases/{id}/costing`
- **Akses:** Case Manager, Costing Analyst
- **Fitur:**
  - View Case Cost Breakdown
  - Compare with Pathway Estimate
  - Compare with Unit Cost
  - Cost Variance Analysis
  - Export Cost Report

#### 5.7.4 Case Variance Analysis

- **Route:** `/cases/{id}/variance`
- **Akses:** Semua role (read)
- **Fitur:**
  - View Planned vs Actual Steps
  - View Cost Comparison
  - View Compliance Percentage
  - View Cost Variance
  - View Tariff Comparison
  - Export Analysis Report

---

### 5.8 Analytics & Improvement

**Icon:** 📈  
**Route:** `/analytics` (parent route)  
**Akses:** Semua role (berbeda per report)  
**Deskripsi:** Laporan, analitik, dan continuous improvement

**Submenu:**

#### 5.8.1 Cost Center Performance

- **Route:** `/analytics/cost-center-performance`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - Pre/Post Allocation Cost Report
  - Cost by Expense Category
  - Trend Analysis
  - Export to Excel/PDF

#### 5.8.2 Allocation Summary

- **Route:** `/analytics/allocation-summary`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Allocation Summary by Period
  - Allocation Flow Diagram
  - Compare Versions
  - Export to Excel/PDF

#### 5.8.3 Unit Cost Summary

- **Route:** `/analytics/unit-cost-summary`
- **Akses:** Costing Analyst, Financial Manager
- **Fitur:**
  - Unit Cost by Department
  - Unit Cost Trend
  - Breakdown Analysis
  - Export to Excel/PDF

#### 5.8.4 Tariff Analytics

- **Route:** `/analytics/tariff-analytics`
- **Akses:** Financial Manager, Admin
- **Fitur:**
  - Internal vs INA-CBG Comparison
  - Tariff by Class
  - Margin Analysis
  - Export to Excel/PDF

#### 5.8.5 Pathway Compliance Dashboard

- **Route:** `/analytics/pathway-compliance`
- **Akses:** Semua role
- **Fitur:**
  - Compliance by Pathway
  - Compliance Trend
  - Compliance by Department
  - Export to Excel/PDF

#### 5.8.6 Case Variance Dashboard

- **Route:** `/analytics/case-variance`
- **Akses:** Semua role
- **Fitur:**
  - Cost Variance by Pathway
  - Cost Variance Trend
  - Variance by Department
  - Export to Excel/PDF

#### 5.8.7 LOS Analysis

- **Route:** `/analytics/los-analysis`
- **Akses:** Semua role
- **Fitur:**
  - Length of Stay by Pathway
  - LOS Trend Analysis
  - LOS vs Cost Correlation
  - Export to Excel/PDF

#### 5.8.8 Continuous Improvement (AI-ready)

- **Route:** `/analytics/continuous-improvement`
- **Akses:** Financial Manager, Admin
- **Deskripsi:** Modul untuk continuous improvement dengan persiapan AI/ML
- **Fitur:**
  - Anomaly Detection
  - Cost Optimization Suggestions
  - Pathway Optimization Recommendations
  - Predictive Analytics (future)
  - AI Insights (future)

---

### 5.9 System Administration

**Icon:** ⚙️  
**Route:** `/admin` (parent route)  
**Akses:** Admin, Superadmin  
**Deskripsi:** Pengaturan sistem dan administrasi

**Submenu:**

#### 5.9.1 Hospitals (Superadmin only)

- **Route:** `/admin/hospitals`
- **Akses:** Superadmin
- **Fitur:**
  - List Hospitals
  - Create/Edit Hospital
  - Set Hospital Configuration
  - Hospital Selection

#### 5.9.2 Users

- **Route:** `/admin/users`
- **Akses:** Admin, Superadmin
- **Fitur:**
  - List Users
  - Create/Edit User
  - Assign Roles
  - Change Password
  - Set Active/Inactive

#### 5.9.3 Roles & Permissions

- **Route:** `/admin/roles`
- **Akses:** Admin, Superadmin
- **Fitur:**
  - List Roles
  - Create/Edit Role
  - Assign Permissions
  - Role Hierarchy

#### 5.9.4 Audit Logs

- **Route:** `/admin/audit-logs`
- **Akses:** Admin, Auditor
- **Fitur:**
  - List Audit Logs (filter by user, action, model, date)
  - View Log Details
  - Export Logs
  - Clear Old Logs

#### 5.9.5 API Tokens

- **Route:** `/admin/api-tokens`
- **Akses:** Admin
- **Fitur:**
  - List API Tokens
  - Create/Revoke Token
  - Set Token Permissions
  - Token Usage Logs

#### 5.9.6 System Settings

- **Route:** `/admin/settings`
- **Akses:** Admin
- **Fitur:**
  - General Settings
  - Financial Year Settings
  - Currency Settings
  - Integration Settings (SIMRS)
  - Backup Settings

---

## 6. Role-Based Menu Visibility (Revised Structure)

### 6.1 Superadmin

- Dashboard (Superadmin View)
- System Administration (Hospitals, Users, Audit Logs, System Settings)

### 6.2 Admin

- Dashboard
- Setup (semua)
- Data Input (read)
- Costing Process (read)
- Tariff Management (semua)
- Clinical Pathways (read)
- Patient Cases (read)
- Analytics & Improvement (semua)
- System Administration (kecuali Hospitals)

### 6.3 Financial Manager

- Dashboard
- Setup (Costing Setup, Service Catalog, JKN/INA-CBG Codes, SIMRS Integration - read)
- Data Input (semua)
- Costing Process (semua)
- Tariff Management (semua)
- Clinical Pathways (read only)
- Patient Cases (read only)
- Analytics & Improvement (semua)

### 6.4 Costing Analyst

- Dashboard
- Setup (Costing Setup, Service Catalog, JKN/INA-CBG Codes - read)
- Data Input (semua)
- Costing Process (semua)
- Tariff Management (Simulation only)
- Clinical Pathways (read only)
- Patient Cases (read only)
- Analytics & Improvement (Cost Center, Allocation, Unit Cost, Compliance, Variance)

### 6.5 Medical Committee

- Dashboard
- Setup (JKN/INA-CBG Codes - read only)
- Clinical Pathways (read, approve)
- Patient Cases (read only)
- Analytics & Improvement (Compliance, Variance, Pathway Performance)

### 6.6 Pathway Designer

- Dashboard
- Setup (Service Catalog, JKN/INA-CBG Codes - read only)
- Clinical Pathways (CRUD)
- Patient Cases (read only)
- Analytics & Improvement (Compliance, Variance, Pathway Performance)

### 6.7 Case Manager

- Dashboard
- Setup (Service Catalog, JKN/INA-CBG Codes - read only)
- Clinical Pathways (read only)
- Patient Cases (CRUD)
- Analytics & Improvement (Compliance, Variance - own cases)

### 6.8 Auditor

- Dashboard (read only)
- Analytics & Improvement (semua - read only)
- System Administration (Audit Logs - read only)

---

## 7. Perbandingan Struktur Menu

### 7.1 Perubahan Utama

| Aspek               | Struktur Lama                   | Struktur Revisi                                    | Keuntungan                                    |
| ------------------- | ------------------------------- | -------------------------------------------------- | --------------------------------------------- |
| **Pengelompokan**   | Master Data terpisah            | Setup (Costing Setup, Service Catalog, JKN, SIMRS) | Lebih logis, semua konfigurasi di satu tempat |
| **Data Input**      | GL & Expense Management         | Data Input + Import Center                         | Lebih jelas, ada pusat import                 |
| **Costing Process** | Allocation & Unit Cost terpisah | Costing Process dengan Pre-Allocation Check        | Alur lebih jelas, validasi sebelum proses     |
| **Analytics**       | Reports & Analytics             | Analytics & Improvement                            | Fokus pada improvement, siap untuk AI/ML      |
| **System Admin**    | Terpisah di akhir               | Tetap terpisah (penting)                           | Konsisten dengan best practice                |

### 7.2 Route Mapping

| Menu Lama                  | Route Lama                  | Menu Baru                            | Route Baru                    |
| -------------------------- | --------------------------- | ------------------------------------ | ----------------------------- |
| Master Data > Cost Centers | `/master-data/cost-centers` | Setup > Costing Setup > Cost Centers | `/setup/costing/cost-centers` |
| GL & Expense Management    | `/gl-expenses`              | Data Input > GL Expenses             | `/data-input/gl-expenses`     |
| Cost Allocation            | `/allocation`               | Costing Process > Allocation Engine  | `/costing-process/allocation` |
| Unit Costing               | `/unit-cost`                | Costing Process > Unit Cost Engine   | `/costing-process/unit-cost`  |
| Reports & Analytics        | `/reports`                  | Analytics & Improvement              | `/analytics`                  |

---

**Dokumen ini akan diupdate seiring dengan perkembangan development.**
