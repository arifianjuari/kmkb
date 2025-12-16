# SIMRS Data Types in KMKB Application

This document provides an overview of all the SIMRS data types available in the KMKB application and where to access them.

## Accessing SIMRS Data

All SIMRS data can be accessed through the navigation menu when logged in as an admin user. The following links are available:

1. **Master Barang** - Contains master item data for medications and medical supplies
   - Navigation: Master Barang
   - Route: `/simrs/master-barang`
   - API Endpoint: `/api/simrs/master-barang`

2. **Tindakan** - Contains medical procedure data for both outpatient and inpatient
   - Navigation: Tindakan
   - Route: `/simrs/tindakan`
   - API Endpoints: 
     - Outpatient: `/api/simrs/tindakan-rawat-jalan`
     - Inpatient: `/api/simrs/tindakan-rawat-inap`

3. **Laboratorium** - Contains laboratory test data
   - Navigation: Laboratorium
   - Route: `/simrs/laboratorium`
   - API Endpoint: `/api/simrs/laboratorium`

4. **Radiologi** - Contains radiology examination data
   - Navigation: Radiologi
   - Route: `/simrs/radiologi`
   - API Endpoint: `/api/simrs/radiologi`

5. **Operasi** - Contains surgical procedure data and pricing
   - Navigation: Operasi
   - Route: `/simrs/operasi`
   - API Endpoint: `/api/simrs/operasi`

## Data Filtering

Currently, the web interface displays all available data for each type. To filter or search for specific items:

1. Use your browser's built-in search functionality (Ctrl+F or Cmd+F) on the page
2. For more advanced filtering, you can use the API endpoints directly with query parameters
3. Future enhancements could include adding UI-based filtering controls

## Data Refresh

Each view includes a "Refresh Data" button that will reload the data from the SIMRS database.

## API Usage

All data is also available through API endpoints which can be used for integration with other systems or for building custom filtering interfaces.

Example API call:
```
curl http://your-kmkb-url/api/simrs/master-barang
```

## Notes

- All data is fetched in real-time from the SIMRS database
- The application uses a read-only connection to ensure data safety
- Data is automatically formatted for better readability (e.g., currency formatting)
