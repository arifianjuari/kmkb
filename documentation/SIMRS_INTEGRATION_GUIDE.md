# SIM RS Integration Guide

This document provides instructions on how to configure the SIM RS database connection for the KMKB application.

## Prerequisites

1. Access to the SIM RS database server
2. Read-only credentials for the SIM RS database
3. Network connectivity between the KMKB application server and the SIM RS database server

## Configuration Steps

### 1. Update Environment Variables

Open the `.env` file in the root of your project and update the following variables with the actual SIM RS database connection details:

```env
# SIM RS Database Connection
SIMRS_DB_HOST=your_actual_simrs_host
SIMRS_DB_PORT=3306
SIMRS_DB_DATABASE=your_actual_simrs_database_name
SIMRS_DB_USERNAME=your_actual_simrs_readonly_username
SIMRS_DB_PASSWORD=your_actual_simrs_readonly_password
```

Replace the placeholder values with the actual connection details provided by your SIM RS system administrator.

### 2. Test the Connection

After updating the environment variables, test the connection using the provided Artisan command:

```bash
php artisan simrs:test-connection
```

If the connection is successful, you should see output similar to:

```
Testing connection to SIMRS database...
✓ Successfully connected to SIMRS database
Fetching sample data...
✓ Fetched 5 master barang records
```

### 3. Verify API Endpoints

Once the connection is working, you can test the API endpoints:

1. Test connection endpoint: `GET /api/simrs/test-connection`
2. Master barang endpoint: `GET /api/simrs/master-barang`
3. Harga jual endpoint: `GET /api/simrs/harga-jual`
4. Tindakan rawat jalan endpoint: `GET /api/simrs/tindakan-rawat-jalan`
5. Tindakan rawat inap endpoint: `GET /api/simrs/tindakan-rawat-inap`
6. Laboratorium endpoint: `GET /api/simrs/laboratorium`
7. Radiologi endpoint: `GET /api/simrs/radiologi`
8. Operasi endpoint: `GET /api/simrs/operasi`
9. All data endpoint: `GET /api/simrs/all`

### 4. Access Web Interface

You can also access the SIM RS data through the web interface:

1. Navigate to `/simrs/master-barang` to view master barang data
2. Similar routes exist for other data types

## Troubleshooting

### Connection Issues

If you're experiencing connection issues:

1. Verify that the SIM RS database server is accessible from your application server
2. Check that the provided credentials are correct
3. Ensure that the SIM RS database user has read-only access to the required tables
4. Verify that any firewall rules allow connections from your application server to the SIM RS database server

### SSL Configuration

If your SIM RS database requires SSL connections, you can configure additional SSL parameters in the `.env` file:

```env
SIMRS_MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
```

## Security Considerations

1. Ensure that the SIM RS database user has only read-only access to prevent accidental data modification
2. Store the SIM RS database credentials securely and never commit them to version control
3. Consider using environment-specific configuration files for different deployment environments

## Data Mapping

The integration maps SIM RS data to the following internal representations:

- Master Barang (Obat/BHP) → Used for cost reference data
- Harga Jual → Current pricing information
- Tindakan (Actions) → Clinical pathway steps
- Laboratorium → Lab test references
- Radiologi → Radiology test references
- Operasi → Surgical procedure references

This mapping allows the KMKB application to leverage real-time data from the SIM RS system while maintaining its own internal data structures for clinical pathways and cost management.
