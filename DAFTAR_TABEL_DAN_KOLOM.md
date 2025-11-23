# Daftar Tabel dan Kolom Data

## 1. users

- id
- name
- email
- email_verified_at
- password
- role
- department
- hospital_id
- remember_token
- created_at
- updated_at

## 2. hospitals

- id
- name
- code
- logo_path
- theme_color
- address
- contact
- is_active
- created_at
- updated_at

## 3. clinical_pathways

- id
- name
- description
- diagnosis_code
- version
- effective_date
- created_by
- status
- hospital_id
- created_at
- updated_at

## 4. pathway_steps

- id
- clinical_pathway_id
- step_order
- display_order
- category
- description
- service_code
- criteria
- estimated_cost
- quantity
- cost_reference_id
- hospital_id
- created_at
- updated_at

## 5. patient_cases

- id
- patient_id
- medical_record_number
- clinical_pathway_id
- admission_date
- discharge_date
- primary_diagnosis
- ina_cbg_code
- actual_total_cost
- ina_cbg_tariff
- additional_diagnoses
- compliance_percentage
- cost_variance
- input_by
- input_date
- hospital_id
- created_at
- updated_at

## 6. case_details

- id
- patient_case_id
- pathway_step_id
- service_item
- service_code
- status
- performed
- quantity
- actual_cost
- service_date
- hospital_id
- created_at
- updated_at

## 7. cost_references

- id
- service_code
- simrs_kode_brng
- service_description
- standard_cost
- purchase_price
- selling_price_unit
- selling_price_total
- unit
- source
- hospital_id
- is_synced_from_simrs
- created_at
- updated_at
- last_synced_at

## 8. audit_logs

- id
- user_id
- activity_type
- entity
- entity_id
- details
- ip_address
- user_agent
- hospital_id
- created_at
- updated_at

## 9. jkn_cbg_codes

- id
- code
- name
- description
- service_type
- severity_level
- grouping_version
- tariff
- is_active
- created_at
- updated_at

## 10. personal_access_tokens

- id
- tokenable_type
- tokenable_id
- name
- token
- abilities
- last_used_at
- expires_at
- created_at
- updated_at

## 11. test_patient_cases

- id
- created_at
- updated_at

