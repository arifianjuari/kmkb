# Visual Struktur Menu

## WebApp Costing, Tariff, and Clinical Pathway Management System

Struktur menu dalam format tree untuk referensi cepat.

---

## 📊 Struktur Menu Lengkap

```
📊 Dashboard
│
📋 Master Data
├── 🏢 Cost Centers
├── 📑 Expense Categories
├── ⚙️ Allocation Drivers
├── 💵 Tariff Classes
├── 📦 Cost References ✅
└── 🏥 JKN CBG Codes ✅
│
💰 GL & Expense Management
├── 💰 GL Expenses
├── 📊 Driver Statistics
└── 📈 Service Volumes
│
🔄 Cost Allocation
├── 🗺️ Allocation Maps
├── ▶️ Run Allocation
└── 📊 Allocation Results
│
📊 Unit Costing
├── 📈 Service Volumes
├── 🧮 Calculate Unit Cost
└── 📋 Unit Cost Results
│
💵 Tariff Management
├── 🎯 Tariff Simulation
├── ✅ Final Tariffs
└── 🔍 Tariff Explorer
│
🏥 Clinical Pathways ✅
├── 📋 Pathway List ✅
├── 🛠️ Pathway Builder ✅
├── 📊 Pathway Summary
└── ✅ Pathway Approval ✅
│
👤 Patient Cases ✅
├── 📋 Case List ✅
├── 📝 Case Details ✅
└── 📊 Case Analysis ✅
│
📈 Reports & Analytics
├── 📊 Cost Center Performance
├── 🔄 Allocation Results Summary
├── 💰 Unit Cost Summary
├── 💵 Tariff Comparison
├── ✅ Pathway Compliance ✅
├── 📊 Case Variance Analysis ✅
└── 🏥 Pathway Performance ✅
│
⚙️ System Administration
├── 🏢 Hospitals ✅ (Superadmin)
├── 👥 Users ✅
├── 🔐 Roles & Permissions
├── 📋 Audit Logs ✅
├── 🔑 API Tokens
└── ⚙️ System Settings
│
🔌 SIMRS Integration ✅
├── ⚙️ Connection Settings
├── 📦 Data Sources ✅
└── 🔄 Sync Management ✅
```

**Legenda:**

- ✅ = Sudah diimplementasikan
- ⚠️ = Belum diimplementasikan
- (tanpa tanda) = Perlu dikonfirmasi

---

## 🎯 Menu by Role

### 👑 Superadmin

```
📊 Dashboard (Superadmin View)
🏢 Hospitals
👥 Users
📋 Audit Logs
⚙️ System Settings
```

### 🔧 Admin

```
📊 Dashboard
📋 Master Data (semua)
💰 GL & Expense Management
🔄 Cost Allocation
📊 Unit Costing
💵 Tariff Management
🏥 Clinical Pathways
👤 Patient Cases
📈 Reports & Analytics (semua)
⚙️ System Administration (kecuali Hospitals)
🔌 SIMRS Integration
```

### 💼 Financial Manager

```
📊 Dashboard
📋 Master Data (Cost Centers, Expense Categories, Allocation Drivers, Tariff Classes, Cost References, JKN CBG Codes)
💰 GL & Expense Management (semua)
🔄 Cost Allocation (semua)
📊 Unit Costing (semua)
💵 Tariff Management (semua)
🏥 Clinical Pathways (read only)
👤 Patient Cases (read only)
📈 Reports & Analytics (semua)
🔌 SIMRS Integration (read)
```

### 📊 Costing Analyst

```
📊 Dashboard
📋 Master Data (Cost Centers, Expense Categories, Allocation Drivers, Cost References, JKN CBG Codes)
💰 GL & Expense Management (semua)
🔄 Cost Allocation (semua)
📊 Unit Costing (semua)
💵 Tariff Management (Simulation only)
🏥 Clinical Pathways (read only)
👤 Patient Cases (read only)
📈 Reports & Analytics (Cost Center, Allocation, Unit Cost, Compliance, Variance)
```

### 🏥 Medical Committee

```
📊 Dashboard
📋 Master Data (JKN CBG Codes - read only)
🏥 Clinical Pathways (read, approve)
👤 Patient Cases (read only)
📈 Reports & Analytics (Compliance, Variance, Pathway Performance)
```

### 🎨 Pathway Designer

```
📊 Dashboard
📋 Master Data (Cost References, JKN CBG Codes - read only)
🏥 Clinical Pathways (CRUD)
👤 Patient Cases (read only)
📈 Reports & Analytics (Compliance, Variance, Pathway Performance)
```

### 📝 Case Manager

```
📊 Dashboard
📋 Master Data (Cost References, JKN CBG Codes - read only)
🏥 Clinical Pathways (read only)
👤 Patient Cases (CRUD)
📈 Reports & Analytics (Compliance, Variance - own cases)
```

### 👁️ Auditor

```
📊 Dashboard (read only)
📈 Reports & Analytics (semua - read only)
📋 Audit Logs (read only)
```

---

## 🔄 Workflow-Based Menu Grouping

### Setup Phase

```
📋 Master Data
├── Cost Centers
├── Expense Categories
├── Allocation Drivers
├── Tariff Classes
├── Cost References
└── JKN CBG Codes
```

### Data Input Phase

```
💰 GL & Expense Management
├── GL Expenses
├── Driver Statistics
└── Service Volumes
```

### Processing Phase

```
🔄 Cost Allocation
├── Allocation Maps
├── Run Allocation
└── Allocation Results

📊 Unit Costing
├── Service Volumes
├── Calculate Unit Cost
└── Unit Cost Results
```

### Output Phase

```
💵 Tariff Management
├── Tariff Simulation
├── Final Tariffs
└── Tariff Explorer
```

### Clinical Phase

```
🏥 Clinical Pathways
├── Pathway List
├── Pathway Builder
├── Pathway Summary
└── Pathway Approval

👤 Patient Cases
├── Case List
├── Case Details
└── Case Analysis
```

### Analysis Phase

```
📈 Reports & Analytics
├── Cost Center Performance
├── Allocation Results Summary
├── Unit Cost Summary
├── Tariff Comparison
├── Pathway Compliance
├── Case Variance Analysis
└── Pathway Performance
```

---

## 📱 Responsive Menu Structure

### Desktop (Horizontal Navigation)

```
[Logo] [Dashboard] [Master Data ▼] [GL & Expenses ▼] [Allocation ▼] [Unit Cost ▼] [Tariff ▼] [Pathways ▼] [Cases ▼] [Reports ▼] [Admin ▼] [SIMRS ▼] [User ▼]
```

### Mobile (Hamburger Menu)

```
☰ Menu
├── 📊 Dashboard
├── 📋 Master Data
│   ├── Cost Centers
│   ├── Expense Categories
│   ├── Allocation Drivers
│   ├── Tariff Classes
│   ├── Cost References
│   └── JKN CBG Codes
├── 💰 GL & Expense Management
│   ├── GL Expenses
│   ├── Driver Statistics
│   └── Service Volumes
├── 🔄 Cost Allocation
│   ├── Allocation Maps
│   ├── Run Allocation
│   └── Allocation Results
├── 📊 Unit Costing
│   ├── Service Volumes
│   ├── Calculate Unit Cost
│   └── Unit Cost Results
├── 💵 Tariff Management
│   ├── Tariff Simulation
│   ├── Final Tariffs
│   └── Tariff Explorer
├── 🏥 Clinical Pathways
│   ├── Pathway List
│   ├── Pathway Builder
│   ├── Pathway Summary
│   └── Pathway Approval
├── 👤 Patient Cases
│   ├── Case List
│   ├── Case Details
│   └── Case Analysis
├── 📈 Reports & Analytics
│   ├── Cost Center Performance
│   ├── Allocation Results Summary
│   ├── Unit Cost Summary
│   ├── Tariff Comparison
│   ├── Pathway Compliance
│   ├── Case Variance Analysis
│   └── Pathway Performance
├── ⚙️ System Administration
│   ├── Hospitals
│   ├── Users
│   ├── Roles & Permissions
│   ├── Audit Logs
│   ├── API Tokens
│   └── System Settings
└── 🔌 SIMRS Integration
    ├── Connection Settings
    ├── Data Sources
    └── Sync Management
```

---

## 🎨 Menu Icons Reference

| Menu              | Icon | Unicode/Class       |
| ----------------- | ---- | ------------------- |
| Dashboard         | 📊   | `chart-bar`         |
| Master Data       | 📋   | `clipboard-list`    |
| GL & Expenses     | 💰   | `currency-dollar`   |
| Cost Allocation   | 🔄   | `arrow-path`        |
| Unit Costing      | 📊   | `calculator`        |
| Tariff            | 💵   | `banknotes`         |
| Clinical Pathways | 🏥   | `building-hospital` |
| Patient Cases     | 👤   | `user`              |
| Reports           | 📈   | `chart-line`        |
| System Admin      | ⚙️   | `cog-6-tooth`       |
| SIMRS             | 🔌   | `plug`              |

---

## 📝 Implementation Checklist

### Menu Items to Implement

#### Master Data

- [ ] Cost Centers menu & pages
- [ ] Expense Categories menu & pages
- [ ] Allocation Drivers menu & pages
- [ ] Tariff Classes menu & pages

#### GL & Expense Management

- [ ] GL Expenses menu & pages
- [ ] Driver Statistics menu & pages
- [ ] Service Volumes menu & pages (dedicated)

#### Cost Allocation

- [ ] Allocation Maps menu & pages
- [ ] Run Allocation menu & pages
- [ ] Allocation Results menu & pages

#### Unit Costing

- [ ] Calculate Unit Cost menu & pages
- [ ] Unit Cost Results menu & pages

#### Tariff Management

- [ ] Tariff Simulation menu & pages
- [ ] Final Tariffs menu & pages
- [ ] Tariff Explorer menu & pages

#### Clinical Pathways

- [ ] Pathway Summary menu & pages

#### Reports

- [ ] Cost Center Performance menu & pages
- [ ] Allocation Results Summary menu & pages
- [ ] Unit Cost Summary menu & pages
- [ ] Tariff Comparison menu & pages

#### System Administration

- [ ] Roles & Permissions menu & pages
- [ ] API Tokens menu & pages
- [ ] System Settings menu & pages

#### SIMRS Integration

- [ ] Connection Settings menu & pages

---

**Dokumen ini melengkapi MENU-STRUCTURE-DESIGN.md dengan visualisasi yang lebih mudah dipahami.**
