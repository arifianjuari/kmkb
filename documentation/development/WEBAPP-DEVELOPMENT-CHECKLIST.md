# WebApp Development Checklist

## Based on BRD, PRD, and SPEC (Costing, Tariff, Clinical Pathway System)

This checklist is designed so **AgentAI / cursorAI** can track what has been completed and what is pending during development.  
Mark each task as:

- [ ] Pending
- [x] Done
- [~] In Progress

---

# 1. Project Setup

## 1.1 Repository & Environment

- [x] Initialize Git repository
- [x] Setup backend framework (Laravel / Node / NestJS / others)
- [x] Setup frontend framework (React / Vue / Next / Nuxt)
- [x] Setup MySQL database connection
- [x] Create `.env` configuration
- [x] Setup authentication scaffolding
- [x] Create initial directory structure (backend + frontend)

---

# 2. Database & Migrations (Based on SPEC)

## 2.1 Core / Auth Tables

- [x] hospitals
- [x] users
- [x] audit_logs
- [x] personal_access_tokens

## 2.2 Master Data Tables

- [ ] cost_centers
- [ ] expense_categories
- [ ] allocation_drivers
- [ ] tariff_classes
- [x] cost_references
- [x] jkn_cbg_codes

## 2.3 GL & Driver Tables

- [ ] gl_expenses
- [ ] driver_statistics
- [ ] service_volumes

## 2.4 Cost Allocation Tables

- [ ] allocation_maps
- [ ] allocation_results

## 2.5 Unit Costing Tables

- [ ] unit_cost_calculations

## 2.6 Tariff Tables

- [ ] final_tariffs

## 2.7 Clinical Pathway Tables

- [x] clinical_pathways
- [x] pathway_steps
- [ ] pathway_tariff_summaries

## 2.8 Patient Case Tables

- [x] patient_cases
- [x] case_details

---

# 3. Backend API Development

## 3.1 Authentication & User Management

- [x] Login API
- [x] Logout API
- [x] Register / invite user API
- [x] Role & permission structure
- [x] Audit logging mechanism

## 3.2 Master Data APIs

- [x] CRUD: hospitals
- [ ] CRUD: cost_centers
- [ ] CRUD: expense_categories
- [ ] CRUD: allocation_drivers
- [ ] CRUD: tariff_classes
- [x] CRUD: cost_references
- [x] Import cost references from SIMRS
- [x] CRUD: jkn_cbg_codes

## 3.3 GL & Drivers APIs

- [ ] Import GL expenses (CSV/XLSX)
- [ ] CRUD: gl_expenses
- [ ] CRUD: driver_statistics
- [ ] CRUD: service_volumes

## 3.4 Cost Allocation Engine

- [ ] CRUD: allocation_maps
- [ ] Run allocation engine
- [ ] Allocation recompute / versioning
- [ ] View allocation results

## 3.5 Unit Costing Engine

- [ ] Unit cost compute API
- [ ] Versioning support
- [ ] Unit cost result API

## 3.6 Tariff Setting APIs

- [ ] Draft tariff simulation API
- [ ] Save final tariff API
- [ ] Get tariffs by class / service
- [ ] SK-based tariff approval tracking

## 3.7 Clinical Pathway APIs

- [x] CRUD: clinical_pathways
- [x] CRUD: pathway_steps
- [ ] Auto-populate estimated_cost from unit cost
- [ ] Recalculate pathway summary
- [x] Approve pathway workflow

## 3.8 Patient Case APIs

- [x] CRUD: patient_cases
- [x] Generate planned steps from pathway
- [x] CRUD: case_details
- [ ] Auto-fill unit_cost_applied & tariff_applied
- [x] Recalculate variance & compliance

## 3.9 Analytics APIs

- [ ] Cost center performance API
- [ ] Unit cost trend API
- [ ] Tariff vs INA-CBG comparison API
- [x] Pathway compliance API
- [x] Patient case variance report API

---

# 4. Frontend Development

## 4.1 Global Components

- [x] Login / Authentication pages
- [x] Sidebar navigation
- [x] Top navbar / breadcrumbs
- [x] User profile page
- [x] Role-based menu rendering
- [ ] Notification system

## 4.2 Dashboard Pages

- [x] Executive summary dashboard
- [ ] Cost center performance widget
- [ ] Unit cost overview widget
- [ ] Tariff summary widget
- [x] Clinical pathway compliance widget

## 4.3 Master Data Pages

- [x] hospitals CRUD UI
- [ ] cost centers management UI
- [ ] expense categories UI
- [ ] allocation drivers UI
- [ ] tariff classes UI
- [x] cost references UI + SIMRS sync UI
- [x] jkn cbg code UI

## 4.4 GL & Drivers Pages

- [ ] GL expenses upload & table
- [ ] Driver statistics input & table
- [ ] Service volume input & table

## 4.5 Allocation Pages

- [ ] Allocation map editor
- [ ] Run allocation page
- [ ] Allocation results viewer

## 4.6 Unit Cost Pages

- [ ] Volume input viewer
- [ ] Unit cost calculation run UI
- [ ] Unit cost result pages

## 4.7 Tariff Pages

- [ ] Tarif simulation tool (margin calculator)
- [ ] Final tariff entry UI
- [ ] Tariff explorer/filter table
- [ ] SK tariff approval tracking

## 4.8 Pathway Pages

- [x] Pathway list & status filter
- [x] Create/edit pathway page
- [x] Step editor (with service auto-complete)
- [ ] Pathway tariff summary viewer
- [x] Approval workflow UI

## 4.9 Patient Case Pages

- [x] Patient case list
- [x] Create case form
- [x] Case detail editor
- [x] Variance & compliance page

## 4.10 Reporting Pages

- [ ] Cost allocation report
- [ ] Unit cost report
- [ ] Tariff report
- [x] Pathway compliance report
- [x] Case variance report

---

# 5. Business Logic / Services

## 5.1 Allocation Engine

- [ ] Fetch cost centers & drivers
- [ ] Compute cost distribution
- [ ] Apply step sequence
- [ ] Write into allocation_results
- [ ] Support recalculation

## 5.2 Unit Cost Engine

- [ ] Merge GL + allocation results
- [ ] Map cost center → services
- [ ] Compute per-service unit cost
- [ ] Save versioned results

## 5.3 Tariff Engine

- [ ] Apply margin
- [ ] Split margin into components
- [ ] Generate final tariff

## 5.4 Pathway Engine

- [x] Build pathway skeleton
- [ ] Auto-fill estimated cost
- [ ] Compute total estimated cost & tariff

## 5.5 Patient Case Engine

- [x] Generate planned services
- [x] Compare performed vs planned
- [x] Compute compliance
- [x] Compute cost variance

---

# 6. Integrations

- [x] SIMRS cost reference sync
- [ ] SIMRS patient registration sync (optional)
- [ ] CSV/XLSX importer for GL & volumes
- [x] PDF export for reports
- [x] Excel export for financial data

---

# 7. Testing

## 7.1 Backend Testing

- [x] Unit tests
- [x] API integration tests
- [x] Database migration tests
- [ ] Calculation logic tests (allocation, unit cost, tariff)

## 7.2 Frontend Testing

- [ ] Component tests
- [ ] Form validation tests
- [ ] API mocking tests

---

# 8. Deployment

- [~] CI/CD pipeline setup
- [x] Build frontend assets
- [x] Database migration pipeline
- [~] Backup & rollback strategy
- [~] Production environment configuration

---

# 9. Documentation

- [ ] API documentation (Swagger / OpenAPI)
- [ ] User manual
- [x] Developer technical documentation
- [ ] Architecture diagram
- [ ] ERD diagram
- [x] Maintenance and troubleshooting guide

---

# 10. Final Acceptance Checklist

- [ ] All APIs match SPEC
- [ ] All DB tables match SPEC
- [ ] All pages match PRD
- [ ] All business rules match BRD
- [ ] Pathway workflow approved by Medical Committee
- [ ] Tariff workflow approved by Director
- [ ] All unit cost & allocation results reproducible
- [ ] All reports validated with sample real data

---

**This file helps AI agents track development progress and avoid duplication.**
