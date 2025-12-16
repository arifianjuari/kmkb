# SIMRS Cost Reference Synchronization

This document describes the process of synchronizing cost reference data from the SIMRS system to the KMKB clinical pathways system. The synchronization can be performed either automatically through scheduled tasks or manually through the admin interface. synchronization ensures that master price data from SIMRS is used as the authoritative source for clinical pathway cost calculations.

## How It Works

1. **Data Source**: The synchronization pulls master barang (item) data from the SIMRS database, including item codes, descriptions, and pricing information.

2. **Tracking Columns**: Additional columns have been added to the `cost_references` table to track synchronization status:
   - `simrs_kode_brng` (string, nullable): Stores the SIMRS item code
   - `is_synced_from_simrs` (boolean): Flags records that were synced from SIMRS
   - `last_synced_at` (timestamp, nullable): Tracks the last synchronization time
   - `hospital_id` (foreign key, nullable): Links cost references to specific hospitals
   - `purchase_price` (decimal, nullable): The purchase price from SIMRS
   - `selling_price_unit` (decimal, nullable): The selling price per unit from SIMRS
   - `selling_price_total` (decimal, nullable): The total selling price from SIMRS

3. **Synchronization Process**: The `cost-references:sync-from-simrs` Artisan command performs the synchronization:
   - Connects to the SIMRS database
   - Fetches master barang data
   - Creates or updates records in the `cost_references` table
   - Sets appropriate tracking flags

## Commands

### Manual Synchronization

Administrators can manually trigger synchronization either through the command line or through the web interface.

### Command Line

```bash
php artisan cost-references:sync-from-simrs {--limit=100} {--hospital-id=} {--use-user-hospital}
```

Options:
- `--limit`: Number of records to sync (default: 100)
- `--hospital-id`: Hospital ID to sync for (optional)
- `--use-user-hospital`: Use the hospital ID from the authenticated user (web interface only)

### Web Interface

Administrators can access the synchronization interface at `/simrs/sync` where they can:
- Set the number of records to sync
- Specify a hospital ID for hospital-specific synchronization
- Trigger the synchronization process with a button click
- View the results of the synchronization

The web interface automatically uses the authenticated user's hospital ID if no specific hospital ID is provided. This ensures that each user only syncs data for their assigned hospital.

### View Synced Data
```bash
# Show synced cost references
php artisan cost-references:show-simrs

# Show with custom limit
php artisan cost-references:show-simrs --limit=20
```

## Scheduling

The synchronization is scheduled to run automatically daily at 2:00 AM via Laravel's task scheduler. The schedule is defined in `app/Console/Kernel.php`.

To manually run the scheduler for testing:
```bash
php artisan schedule:run
```

## Database Structure

### Modified Tables

#### cost_references
- Added `simrs_kode_brng` (string, nullable)
- Added `is_synced_from_simrs` (boolean, default: false)
- Added `last_synced_at` (timestamp, nullable)
- Updated `hospital_id` to allow null values with default null

## Troubleshooting

### Common Issues

1. **Connection Errors**: Verify SIMRS database credentials in `.env` file
2. **Data Not Syncing**: Check that the SIMRS database is accessible and contains data
3. **Permission Errors**: Ensure the application has write permissions to the database

### Logs

Synchronization errors are logged to `storage/logs/laravel.log`. Look for entries containing "SIMRS" for synchronization-related issues.
