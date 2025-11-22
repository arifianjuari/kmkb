<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clinical Pathway PDF</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        h2 { font-size: 14px; margin: 20px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px 8px; vertical-align: top; }
        th { background: #f2f2f2; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta th, .meta td { border: none; padding: 4px 6px; }
        .right { text-align: right; }
        .small { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <h1>Clinical Pathway</h1>

    <h2>Pathway Information</h2>
    <table class="meta">
        <tr>
            <th style="width: 160px">Name</th>
            <td><?php echo e($pathway->name); ?></td>
        </tr>
        <tr>
            <th>Diagnosis Code</th>
            <td><?php echo e($pathway->diagnosis_code); ?></td>
        </tr>
        <tr>
            <th>Version</th>
            <td><?php echo e($pathway->version); ?></td>
        </tr>
        <tr>
            <th>Effective Date</th>
            <td><?php echo e(optional($pathway->effective_date)->format('d M Y')); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo e(ucfirst($pathway->status)); ?></td>
        </tr>
        <tr>
            <th>Created By</th>
            <td><?php echo e(optional($pathway->creator)->name); ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?php echo e($pathway->description); ?></td>
        </tr>
    </table>

    <h2>Pathway Steps</h2>
    <?php if($pathway->steps->count() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Day</th>
                    <th style="width: 18%">Activity</th>
                    <th style="width: 34%">Description</th>
                    <th style="width: 20%">Criteria</th>
                    <th style="width: 10%" class="right">Standard Cost</th>
                    <th style="width: 10%" class="right">Full Standard Cost</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $total = 0;
            ?>
            <?php $__currentLoopData = $pathway->steps->sortBy('step_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $lineTotal = (int)($step->estimated_cost ?? 0) * (int)($step->quantity ?? 1);
                    $total += $lineTotal;
                ?>
                <tr>
                    <td><?php echo e($step->step_order); ?></td>
                    <td><?php echo e($step->service_code); ?></td>
                    <td><?php echo e($step->description); ?></td>
                    <td><?php echo e($step->criteria); ?></td>
                    <td class="right">Rp<?php echo e(number_format($step->estimated_cost, 0, ',', '.')); ?></td>
                    <td class="right">Rp<?php echo e(number_format($lineTotal, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="right"><strong>Total Standard Cost:</strong></td>
                    <td class="right"><strong>Rp<?php echo e(number_format($total, 0, ',', '.')); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p class="small">No steps defined for this pathway yet.</p>
    <?php endif; ?>

    <p class="small" style="margin-top: 16px">Generated on <?php echo e(now()->format('d M Y H:i')); ?></p>
</body>
</html>
<?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/pathways/pdf.blade.php ENDPATH**/ ?>