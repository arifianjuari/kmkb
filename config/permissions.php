<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Menu Structure
    |--------------------------------------------------------------------------
    |
    | Define all menus and submenus with their routes and required permissions.
    | Each menu can have submenus that also require specific permissions.
    |
    */

    'menus' => [
                'dashboard' => [
                    'route' => 'dashboard',
                    'permission' => 'view-dashboard',
                ],

                'setup' => [
                    'route' => 'setup.*',
                    'permission' => 'view-setup',
                    'submenus' => [
                            'cost-centers' => [
                                'route' => 'cost-centers.*',
                                'permission' => 'view-cost-centers',
                            ],
                            'expense-categories' => [
                                'route' => 'expense-categories.*',
                                'permission' => 'view-expense-categories',
                            ],
                            'allocation-drivers' => [
                                'route' => 'allocation-drivers.*',
                                'permission' => 'view-allocation-drivers',
                            ],
                            'tariff-classes' => [
                                'route' => 'tariff-classes.*',
                                'permission' => 'view-tariff-classes',
                            ],
                            'cost-references' => [
                                'route' => 'cost-references.*',
                                'permission' => 'view-cost-references',
                            ],
                            'service-catalog' => [
                                'route' => 'setup.service-catalog.*',
                                'permission' => 'view-service-catalog',
                            ],
                            'jkn-cbg-codes' => [
                                'route' => 'jkn-cbg-codes.*',
                                'permission' => 'view-jkn-cbg-codes',
                            ],
                            'simrs-integration' => [
                                'route' => 'setup.simrs-integration.*',
                                'permission' => 'view-simrs-integration',
                            ],
                    ],
                ],

                'data-input' => [
                    'route' => 'data-input.*',
                    'permission' => 'view-data-input',
                    'submenus' => [
                            'gl-expenses' => [
                                'route' => 'gl-expenses.*',
                                'permission' => 'view-gl-expenses',
                            ],
                            'driver-statistics' => [
                                'route' => 'driver-statistics.*',
                                'permission' => 'view-driver-statistics',
                            ],
                            'service-volumes' => [
                                'route' => 'service-volumes.*',
                                'permission' => 'view-service-volumes',
                            ],
                            'import-center' => [
                                'route' => 'data-input.import-center',
                                'permission' => 'view-import-center',
                            ],
                    ],
                ],

                'costing-process' => [
                    'route' => 'costing-process.*',
                    'permission' => 'view-costing-process',
                    'submenus' => [
                            'pre-allocation-check' => [
                                'route' => 'costing-process.pre-allocation-check.*',
                                'permission' => 'view-pre-allocation-check',
                            ],
                            'allocation' => [
                                'route' => 'costing-process.allocation.*',
                                'permission' => 'view-allocation',
                            ],
                            'unit-cost' => [
                                'route' => 'costing-process.unit-cost.*',
                                'permission' => 'view-unit-cost',
                            ],
                    ],
                ],

                'tariffs' => [
                    'route' => 'tariffs.*',
                    'permission' => 'view-tariffs',
                    'submenus' => [
                            'tariff-simulation' => [
                                'route' => 'tariff-simulation.*',
                                'permission' => 'view-tariff-simulation',
                            ],
                            'tariff-structure' => [
                                'route' => 'tariffs.structure',
                                'permission' => 'view-tariff-structure',
                            ],
                            'final-tariffs' => [
                                'route' => 'final-tariffs.*',
                                'permission' => 'view-final-tariffs',
                            ],
                            'tariff-explorer' => [
                                'route' => 'tariff-explorer.*',
                                'permission' => 'view-tariff-explorer',
                            ],
                            'tariff-comparison' => [
                                'route' => 'tariffs.comparison',
                                'permission' => 'view-tariff-comparison',
                            ],
                    ],
                ],

                'pathways' => [
                    'route' => 'pathways.*',
                    'permission' => 'view-pathways',
                ],

                'cases' => [
                    'route' => 'cases.*',
                    'permission' => 'view-cases',
                ],

                'analytics' => [
                    'route' => 'analytics.*',
                    'permission' => 'view-analytics',
                    'submenus' => [
                            'cost-center-performance' => [
                                'route' => 'analytics.cost-center-performance',
                                'permission' => 'view-cost-center-performance',
                            ],
                            'allocation-summary' => [
                                'route' => 'analytics.allocation-summary',
                                'permission' => 'view-allocation-summary',
                            ],
                            'unit-cost-summary' => [
                                'route' => 'analytics.unit-cost-summary',
                                'permission' => 'view-unit-cost-summary',
                            ],
                            'tariff-analytics' => [
                                'route' => 'analytics.tariff-analytics',
                                'permission' => 'view-tariff-analytics',
                            ],
                            'pathway-compliance' => [
                                'route' => 'analytics.pathway-compliance',
                                'permission' => 'view-pathway-compliance',
                            ],
                            'case-variance' => [
                                'route' => 'analytics.case-variance',
                                'permission' => 'view-case-variance',
                            ],
                            'los-analysis' => [
                                'route' => 'analytics.los-analysis',
                                'permission' => 'view-los-analysis',
                            ],
                            'continuous-improvement' => [
                                'route' => 'analytics.continuous-improvement',
                                'permission' => 'view-continuous-improvement',
                            ],
                    ],
                ],

                'simrs' => [
                    'route' => 'simrs.*',
                    'permission' => 'view-simrs',
                ],

                'service-volume-current' => [
                    'route' => 'svc-current.*',
                    'permission' => 'view-service-volume-current',
                ],

                'references' => [
                    'route' => 'references.*',
                    'permission' => 'view-references',
                ],

                'users' => [
                    'route' => 'users.*',
                    'permission' => 'view-users',
                ],

                'hospitals' => [
                    'route' => 'hospitals.*',
                    'permission' => 'view-hospitals',
                ],

                'audit-logs' => [
                    'route' => 'audit-logs.*',
                    'permission' => 'view-audit-logs',
                ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | Define permissions for each role. Use '*' for all menus/permissions.
    | Each role should have:
    | - 'menus': array of menu keys the role can access
    | - 'permissions': array of permission names for CRUD operations
    |
    */

    'roles' => [
                'superadmin' => [
                    'menus' => [
                        '*',
                    ],
                    'permissions' => [
                        '*',
                    ],
                ],

                'hospital_admin' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'data-input',
                        'costing-process',
                        'tariffs',
                        'pathways',
                        'cases',
                        'analytics',
                        'simrs',
                        'service-volume-current',
                        'references',
                        'users',
                        'audit-logs',
                    ],
                    'permissions' => [
                        // Allocation
                        'view-allocation',
                        'create-allocation',
                        'update-allocation',
                        'delete-allocation',
                        // Allocation drivers
                        'view-allocation-drivers',
                        'create-allocation-drivers',
                        'update-allocation-drivers',
                        'delete-allocation-drivers',
                        // Allocation summary
                        'view-allocation-summary',
                        // Analytics
                        'view-analytics',
                        // Audit logs
                        'view-audit-logs',
                        'delete-audit-logs',
                        // Case details
                        'view-case-details',
                        'create-case-details',
                        'update-case-details',
                        'delete-case-details',
                        // Case variance
                        'view-case-variance',
                        // Cases
                        'view-cases',
                        'create-cases',
                        'update-cases',
                        'delete-cases',
                        // Continuous improvement
                        'view-continuous-improvement',
                        // Cost center performance
                        'view-cost-center-performance',
                        // Cost centers
                        'view-cost-centers',
                        'create-cost-centers',
                        'update-cost-centers',
                        'delete-cost-centers',
                        // Cost references
                        'view-cost-references',
                        'create-cost-references',
                        'update-cost-references',
                        'delete-cost-references',
                        // Costing process
                        'view-costing-process',
                        // Dashboard
                        'view-dashboard',
                        // Data input
                        'view-data-input',
                        // Driver statistics
                        'view-driver-statistics',
                        'create-driver-statistics',
                        'update-driver-statistics',
                        'delete-driver-statistics',
                        // Expense categories
                        'view-expense-categories',
                        'create-expense-categories',
                        'update-expense-categories',
                        'delete-expense-categories',
                        // Final tariffs
                        'view-final-tariffs',
                        'create-final-tariffs',
                        'update-final-tariffs',
                        'delete-final-tariffs',
                        // Gl expenses
                        'view-gl-expenses',
                        'create-gl-expenses',
                        'update-gl-expenses',
                        'delete-gl-expenses',
                        // Import center
                        'view-import-center',
                        // Jkn cbg codes
                        'view-jkn-cbg-codes',
                        'create-jkn-cbg-codes',
                        'update-jkn-cbg-codes',
                        'delete-jkn-cbg-codes',
                        // Los analysis
                        'view-los-analysis',
                        // Pathway compliance
                        'view-pathway-compliance',
                        // Pathways
                        'view-pathways',
                        'create-pathways',
                        'update-pathways',
                        'delete-pathways',
                        'export-pathways',
                        'approve-pathways',
                        // Pre allocation check
                        'view-pre-allocation-check',
                        // References
                        'view-references',
                        'create-references',
                        'update-references',
                        'delete-references',
                        // Service catalog
                        'view-service-catalog',
                        // Service volume current
                        'view-service-volume-current',
                        'sync-service-volume-current',
                        // Service volumes
                        'view-service-volumes',
                        'create-service-volumes',
                        'update-service-volumes',
                        'delete-service-volumes',
                        // Setup
                        'view-setup',
                        // Simrs
                        'view-simrs',
                        'sync-simrs',
                        // Simrs integration
                        'view-simrs-integration',
                        // Tariff analytics
                        'view-tariff-analytics',
                        // Tariff classes
                        'view-tariff-classes',
                        'create-tariff-classes',
                        'update-tariff-classes',
                        'delete-tariff-classes',
                        // Tariff comparison
                        'view-tariff-comparison',
                        // Tariff explorer
                        'view-tariff-explorer',
                        // Tariff simulation
                        'view-tariff-simulation',
                        'create-tariff-simulation',
                        // Tariff structure
                        'view-tariff-structure',
                        'create-tariff-structure',
                        'update-tariff-structure',
                        // Tariffs
                        'view-tariffs',
                        // Unit cost
                        'view-unit-cost',
                        'create-unit-cost',
                        'update-unit-cost',
                        'delete-unit-cost',
                        // Unit cost summary
                        'view-unit-cost-summary',
                        // Users
                        'view-users',
                        'create-users',
                        'update-users',
                        'delete-users',
                    ],
                ],

                'finance_costing' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'data-input',
                        'costing-process',
                        'tariffs',
                        'analytics',
                        'references',
                    ],
                    'permissions' => [
                        // Allocation
                        'create-allocation',
                        'update-allocation',
                        'view-allocation',
                        // Allocation drivers
                        'create-allocation-drivers',
                        'update-allocation-drivers',
                        'view-allocation-drivers',
                        // Allocation summary
                        'view-allocation-summary',
                        // Analytics
                        'view-analytics',
                        // Cost center performance
                        'view-cost-center-performance',
                        // Cost centers
                        'create-cost-centers',
                        'update-cost-centers',
                        'view-cost-centers',
                        // Cost references
                        'view-cost-references',
                        // Costing process
                        'view-costing-process',
                        // Dashboard
                        'view-dashboard',
                        // Data input
                        'view-data-input',
                        // Driver statistics
                        'create-driver-statistics',
                        'update-driver-statistics',
                        'view-driver-statistics',
                        // Expense categories
                        'create-expense-categories',
                        'update-expense-categories',
                        'view-expense-categories',
                        // Final tariffs
                        'create-final-tariffs',
                        'update-final-tariffs',
                        'view-final-tariffs',
                        // Gl expenses
                        'create-gl-expenses',
                        'update-gl-expenses',
                        'view-gl-expenses',
                        // Jkn cbg codes
                        'view-jkn-cbg-codes',
                        // Pre allocation check
                        'view-pre-allocation-check',
                        // References
                        'view-references',
                        // Service catalog
                        'view-service-catalog',
                        // Service volumes
                        'create-service-volumes',
                        'update-service-volumes',
                        'view-service-volumes',
                        // Setup
                        'view-setup',
                        // Tariff analytics
                        'view-tariff-analytics',
                        // Tariff classes
                        'create-tariff-classes',
                        'update-tariff-classes',
                        'view-tariff-classes',
                        // Tariff comparison
                        'view-tariff-comparison',
                        // Tariff explorer
                        'view-tariff-explorer',
                        // Tariff simulation
                        'create-tariff-simulation',
                        'view-tariff-simulation',
                        // Tariff structure
                        'create-tariff-structure',
                        'update-tariff-structure',
                        'view-tariff-structure',
                        // Tariffs
                        'view-tariffs',
                        // Unit cost
                        'create-unit-cost',
                        'update-unit-cost',
                        'view-unit-cost',
                        // Unit cost summary
                        'view-unit-cost-summary',
                    ],
                ],

                'hr_payroll' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                    ],
                    'permissions' => [
                        // Allocation drivers
                        'view-allocation-drivers',
                        // Cost centers
                        'view-cost-centers',
                        // Dashboard
                        'view-dashboard',
                        // Expense categories
                        'view-expense-categories',
                        // Setup
                        'view-setup',
                        // Tariff classes
                        'view-tariff-classes',
                    ],
                ],

                'facility_asset' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'data-input',
                    ],
                    'permissions' => [
                        // Allocation drivers
                        'view-allocation-drivers',
                        // Cost centers
                        'view-cost-centers',
                        // Dashboard
                        'view-dashboard',
                        // Data input
                        'view-data-input',
                        // Expense categories
                        'view-expense-categories',
                        // Gl expenses
                        'view-gl-expenses',
                        'create-gl-expenses',
                        'update-gl-expenses',
                        // Setup
                        'view-setup',
                    ],
                ],

                'simrs_integration' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'simrs',
                        'service-volume-current',
                    ],
                    'permissions' => [
                        // Cost references
                        'view-cost-references',
                        // Dashboard
                        'view-dashboard',
                        // Service catalog
                        'view-service-catalog',
                        // Service volume current
                        'view-service-volume-current',
                        'sync-service-volume-current',
                        // Setup
                        'view-setup',
                        // Simrs
                        'view-simrs',
                        'sync-simrs',
                        // Simrs integration
                        'view-simrs-integration',
                    ],
                ],

                'support_unit' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'data-input',
                        'costing-process',
                    ],
                    'permissions' => [
                        // Allocation
                        'view-allocation',
                        // Allocation drivers
                        'view-allocation-drivers',
                        // Cost centers
                        'view-cost-centers',
                        // Costing process
                        'view-costing-process',
                        // Dashboard
                        'view-dashboard',
                        // Data input
                        'view-data-input',
                        // Driver statistics
                        'view-driver-statistics',
                        'create-driver-statistics',
                        'update-driver-statistics',
                        // Expense categories
                        'view-expense-categories',
                        // Gl expenses
                        'view-gl-expenses',
                        'create-gl-expenses',
                        'update-gl-expenses',
                        // Pre allocation check
                        'view-pre-allocation-check',
                        // Service volumes
                        'view-service-volumes',
                        'create-service-volumes',
                        'update-service-volumes',
                        // Setup
                        'view-setup',
                        // Unit cost
                        'view-unit-cost',
                    ],
                ],

                'clinical_unit' => [
                    'menus' => [
                        'dashboard',
                        'pathways',
                        'cases',
                        'analytics',
                    ],
                    'permissions' => [
                        // Analytics
                        'view-analytics',
                        // Case details
                        'view-case-details',
                        'create-case-details',
                        'update-case-details',
                        // Case variance
                        'view-case-variance',
                        // Cases
                        'view-cases',
                        'create-cases',
                        'update-cases',
                        // Dashboard
                        'view-dashboard',
                        // Los analysis
                        'view-los-analysis',
                        // Pathway compliance
                        'view-pathway-compliance',
                        // Pathways
                        'view-pathways',
                        'create-pathways',
                        'update-pathways',
                    ],
                ],

                'medrec_claims' => [
                    'menus' => [
                        'dashboard',
                        'cases',
                        'analytics',
                        'references',
                    ],
                    'permissions' => [
                        // Analytics
                        'view-analytics',
                        // Case details
                        'view-case-details',
                        'create-case-details',
                        'update-case-details',
                        // Case variance
                        'view-case-variance',
                        // Cases
                        'view-cases',
                        'create-cases',
                        'update-cases',
                        // Dashboard
                        'view-dashboard',
                        // Pathway compliance
                        'view-pathway-compliance',
                        // References
                        'view-references',
                        'create-references',
                        'update-references',
                    ],
                ],

                'pathway_team' => [
                    'menus' => [
                        'dashboard',
                        'pathways',
                        'cases',
                        'analytics',
                        'references',
                    ],
                    'permissions' => [
                        // Analytics
                        'view-analytics',
                        // Case details
                        'view-case-details',
                        // Case variance
                        'view-case-variance',
                        // Cases
                        'view-cases',
                        // Dashboard
                        'view-dashboard',
                        // Los analysis
                        'view-los-analysis',
                        // Pathway compliance
                        'view-pathway-compliance',
                        // Pathways
                        'view-pathways',
                        'create-pathways',
                        'update-pathways',
                        'export-pathways',
                        'approve-pathways',
                        // References
                        'view-references',
                        'create-references',
                        'update-references',
                    ],
                ],

                'management_auditor' => [
                    'menus' => [
                        'dashboard',
                        'setup',
                        'data-input',
                        'costing-process',
                        'tariffs',
                        'pathways',
                        'cases',
                        'analytics',
                        'simrs',
                        'service-volume-current',
                        'references',
                        'users',
                        'audit-logs',
                    ],
                    'permissions' => [
                        // Allocation
                        'view-allocation',
                        // Allocation drivers
                        'view-allocation-drivers',
                        // Allocation summary
                        'view-allocation-summary',
                        // Analytics
                        'view-analytics',
                        // Audit logs
                        'view-audit-logs',
                        // Case details
                        'view-case-details',
                        // Case variance
                        'view-case-variance',
                        // Cases
                        'view-cases',
                        // Continuous improvement
                        'view-continuous-improvement',
                        // Cost center performance
                        'view-cost-center-performance',
                        // Cost centers
                        'view-cost-centers',
                        // Cost references
                        'view-cost-references',
                        // Costing process
                        'view-costing-process',
                        // Dashboard
                        'view-dashboard',
                        // Data input
                        'view-data-input',
                        // Driver statistics
                        'view-driver-statistics',
                        // Expense categories
                        'view-expense-categories',
                        // Gl expenses
                        'view-gl-expenses',
                        // Jkn cbg codes
                        'view-jkn-cbg-codes',
                        // Los analysis
                        'view-los-analysis',
                        // Pathway compliance
                        'view-pathway-compliance',
                        // Pathways
                        'view-pathways',
                        // Pre allocation check
                        'view-pre-allocation-check',
                        // References
                        'view-references',
                        // Service catalog
                        'view-service-catalog',
                        // Service volume current
                        'view-service-volume-current',
                        // Service volumes
                        'view-service-volumes',
                        // Setup
                        'view-setup',
                        // Simrs
                        'view-simrs',
                        // Tariff analytics
                        'view-tariff-analytics',
                        // Tariff classes
                        'view-tariff-classes',
                        // Tariff comparison
                        'view-tariff-comparison',
                        // Tariff explorer
                        'view-tariff-explorer',
                        // Tariff simulation
                        'view-tariff-simulation',
                        // Tariffs
                        'view-tariffs',
                        // Unit cost
                        'view-unit-cost',
                        // Unit cost summary
                        'view-unit-cost-summary',
                    ],
                ],

    ],
];
