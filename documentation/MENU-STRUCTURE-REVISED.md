# Struktur Menu Revisi - Quick Reference

## WebApp Costing, Tariff, and Clinical Pathway Management System

Dokumen ringkas struktur menu revisi untuk referensi implementasi cepat.

---

## 📋 Struktur Menu Utama (9 Menu)

```
1. 📊 Dashboard
2. ⚙️ Setup
3. 📥 Data Input
4. 🔄 Costing Process
5. 💵 Tariff Management
6. 🏥 Clinical Pathways
7. 👤 Patient Cases
8. 📈 Analytics & Improvement
9. ⚙️ System Administration
```

---

## 1. Dashboard

**Route:** `/dashboard`

**Widget:**

- Executive KPIs
- Costing & Tariff Overview
- Clinical Pathway Compliance
- Case Variance Alerts

---

## 2. Setup

**Route:** `/setup`

### 2.1 Costing Setup

- Cost Centers (`/setup/costing/cost-centers`)
- Expense Categories (`/setup/costing/expense-categories`)
- Allocation Drivers (`/setup/costing/allocation-drivers`)
- Tariff Classes (`/setup/costing/tariff-classes`)

### 2.2 Service Catalog

- Service Items (`/setup/service-catalog/service-items`) - formerly Cost References
- SIMRS-linked Items (`/setup/service-catalog/simrs-linked`)
- Import/Export (`/setup/service-catalog/import-export`)

### 2.3 JKN / INA-CBG Codes

- CBG List (`/setup/jkn-cbg-codes/list`)
- Base Tariff Reference (`/setup/jkn-cbg-codes/base-tariff`)

### 2.4 SIMRS Integration

- Connection Settings (`/setup/simrs-integration/settings`)
- Data Sources (`/setup/simrs-integration/data-sources`)
- Sync Management (`/setup/simrs-integration/sync`)

---

## 3. Data Input

**Route:** `/data-input`

- GL Expenses (`/data-input/gl-expenses`)
- Driver Statistics (`/data-input/driver-statistics`)
- Service Volumes (`/data-input/service-volumes`)
- Import Center (`/data-input/import-center`)

---

## 4. Costing Process

**Route:** `/costing-process`

### 4.1 Pre-Allocation Check

- GL Completeness (`/costing-process/pre-allocation-check/gl-completeness`)
- Driver Completeness (`/costing-process/pre-allocation-check/driver-completeness`)
- Service Volume Completeness (`/costing-process/pre-allocation-check/service-volume-completeness`)
- Mapping Validation (`/costing-process/pre-allocation-check/mapping-validation`)

### 4.2 Allocation Engine

- Allocation Maps (`/costing-process/allocation/maps`)
- Run Allocation (`/costing-process/allocation/run`)
- Allocation Results (`/costing-process/allocation/results`)

### 4.3 Unit Cost Engine

- Calculate Unit Cost (`/costing-process/unit-cost/calculate`)
- Unit Cost Results (`/costing-process/unit-cost/results`)
- Compare Unit Cost Versions (`/costing-process/unit-cost/compare`)

---

## 5. Tariff Management

**Route:** `/tariffs`

- Tariff Simulation (`/tariffs/simulation`)
- Tariff Structure Setup (`/tariffs/structure`)
- Final Tariffs (`/tariffs/final`)
- Tariff Explorer (`/tariffs/explorer`)
- Tariff vs INA-CBG Comparison (`/tariffs/comparison`)

---

## 6. Clinical Pathways

**Route:** `/pathways`

- Pathway Repository (`/pathways`)
- Pathway Builder (`/pathways/{id}/builder`)
- Pathway Cost Summary (`/pathways/{id}/summary`)
- Pathway Approval (`/pathways/{id}/approval`)
- Template Import/Export (`/pathways/templates`)

---

## 7. Patient Cases

**Route:** `/cases`

- Case Registration (`/cases`)
- Case Details (`/cases/{id}/details`)
- Case Costing (`/cases/{id}/costing`)
- Case Variance Analysis (`/cases/{id}/variance`)

---

## 8. Analytics & Improvement

**Route:** `/analytics`

- Cost Center Performance (`/analytics/cost-center-performance`)
- Allocation Summary (`/analytics/allocation-summary`)
- Unit Cost Summary (`/analytics/unit-cost-summary`)
- Tariff Analytics (`/analytics/tariff-analytics`)
- Pathway Compliance Dashboard (`/analytics/pathway-compliance`)
- Case Variance Dashboard (`/analytics/case-variance`)
- LOS Analysis (`/analytics/los-analysis`)
- Continuous Improvement (AI-ready) (`/analytics/continuous-improvement`)

---

## 9. System Administration

**Route:** `/admin`

- Hospitals (`/admin/hospitals`) - Superadmin only
- Users (`/admin/users`)
- Roles & Permissions (`/admin/roles`)
- Audit Logs (`/admin/audit-logs`)
- API Tokens (`/admin/api-tokens`)
- System Settings (`/admin/settings`)

---

## 🔄 Route Migration Mapping

### Master Data → Setup

| Old Route                         | New Route                              |
| --------------------------------- | -------------------------------------- |
| `/master-data/cost-centers`       | `/setup/costing/cost-centers`          |
| `/master-data/expense-categories` | `/setup/costing/expense-categories`    |
| `/master-data/allocation-drivers` | `/setup/costing/allocation-drivers`    |
| `/master-data/tariff-classes`     | `/setup/costing/tariff-classes`        |
| `/cost-references`                | `/setup/service-catalog/service-items` |
| `/jkn-cbg-codes`                  | `/setup/jkn-cbg-codes/list`            |

### GL & Expense Management → Data Input

| Old Route                        | New Route                       |
| -------------------------------- | ------------------------------- |
| `/gl-expenses`                   | `/data-input/gl-expenses`       |
| `/gl-expenses/driver-statistics` | `/data-input/driver-statistics` |
| `/gl-expenses/service-volumes`   | `/data-input/service-volumes`   |

### Cost Allocation → Costing Process

| Old Route             | New Route                             |
| --------------------- | ------------------------------------- |
| `/allocation/maps`    | `/costing-process/allocation/maps`    |
| `/allocation/run`     | `/costing-process/allocation/run`     |
| `/allocation/results` | `/costing-process/allocation/results` |

### Unit Costing → Costing Process

| Old Route              | New Route                              |
| ---------------------- | -------------------------------------- |
| `/unit-cost/calculate` | `/costing-process/unit-cost/calculate` |
| `/unit-cost/results`   | `/costing-process/unit-cost/results`   |

### Reports → Analytics

| Old Route                          | New Route                            |
| ---------------------------------- | ------------------------------------ |
| `/reports/cost-center-performance` | `/analytics/cost-center-performance` |
| `/reports/allocation-summary`      | `/analytics/allocation-summary`      |
| `/reports/unit-cost-summary`       | `/analytics/unit-cost-summary`       |
| `/reports/tariff-comparison`       | `/analytics/tariff-analytics`        |
| `/reports/compliance`              | `/analytics/pathway-compliance`      |
| `/reports/cost-variance`           | `/analytics/case-variance`           |
| `/reports/pathway-performance`     | `/analytics/los-analysis`            |

### SIMRS Integration → Setup

| Old Route         | New Route                               |
| ----------------- | --------------------------------------- |
| `/simrs/settings` | `/setup/simrs-integration/settings`     |
| `/simrs`          | `/setup/simrs-integration/data-sources` |
| `/simrs/sync`     | `/setup/simrs-integration/sync`         |

---

## ✅ Checklist Implementasi

### Phase 1: Route Migration

- [ ] Update route definitions di `routes/web.php`
- [ ] Update navigation menu component
- [ ] Update breadcrumbs
- [ ] Add route redirects untuk backward compatibility

### Phase 2: Menu Restructuring

- [ ] Update sidebar navigation
- [ ] Update top navigation bar
- [ ] Update mobile hamburger menu
- [ ] Update role-based menu visibility

### Phase 3: New Features

- [ ] Implement Pre-Allocation Check module
- [ ] Implement Import Center
- [ ] Implement Tariff Structure Setup
- [ ] Implement Continuous Improvement module (basic)

### Phase 4: Testing

- [ ] Test all routes
- [ ] Test role-based access
- [ ] Test navigation flow
- [ ] Test mobile responsiveness

---

## 📝 Catatan Implementasi

1. **Backward Compatibility**: Gunakan route redirects untuk route lama agar tidak break existing bookmarks/links
2. **Gradual Migration**: Bisa dilakukan secara bertahap per modul
3. **User Training**: Perlu update dokumentasi user guide untuk perubahan struktur menu
4. **Role Permissions**: Pastikan semua role permissions tetap berfungsi setelah restructure

---

**Dokumen ini adalah quick reference. Untuk detail lengkap, lihat MENU-STRUCTURE-DESIGN.md bagian 5.**




