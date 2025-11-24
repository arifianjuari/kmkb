<?php $__env->startSection('content'); ?>
<section class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('Users')); ?></h2>
            <a href="<?php echo e(route('users.create')); ?>" class="btn-primary">
                <?php echo e(__('Create New User')); ?>

            </a>
        </div>
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <form method="GET" action="<?php echo e(route('users.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Name')); ?></label>
                    <input type="text" id="name" name="name" value="<?php echo e(request('name')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Email')); ?></label>
                    <input type="email" id="email" name="email" value="<?php echo e(request('email')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Role')); ?></label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value=""><?php echo e(__('All Roles')); ?></option>
                        <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>><?php echo e(__('Admin')); ?></option>
                        <option value="mutu" <?php echo e(request('role') == 'mutu' ? 'selected' : ''); ?>><?php echo e(__('Mutu')); ?></option>
                        <option value="klaim" <?php echo e(request('role') == 'klaim' ? 'selected' : ''); ?>><?php echo e(__('Klaim')); ?></option>
                        <option value="manajemen" <?php echo e(request('role') == 'manajemen' ? 'selected' : ''); ?>><?php echo e(__('Manajemen')); ?></option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-2 md:col-span-3">
                    <button type="submit" class="btn-primary">
                        <?php echo e(__('Filter')); ?>

                    </button>
                    <a href="<?php echo e(route('users.index')); ?>" class="btn-secondary">
                        <?php echo e(__('Clear')); ?>

                    </a>
                </div>
            </form>
        </div>
        
        <div class="p-6">
            <?php if($users->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Name')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Email')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Role')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Department')); ?></th>
                                <?php if(auth()->user()->isSuperadmin()): ?>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Hospital')); ?></th>
                                <?php endif; ?>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Created At')); ?></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($user->name); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($user->email); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if($user->role === 'admin'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100"><?php echo e(__('Admin')); ?></span>
                                        <?php elseif($user->role === 'mutu'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100"><?php echo e(__('Mutu')); ?></span>
                                        <?php elseif($user->role === 'klaim'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100"><?php echo e(__('Klaim')); ?></span>
                                        <?php elseif($user->role === 'manajemen'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100"><?php echo e(__('Manajemen')); ?></span>
                                        <?php elseif($user->role === 'superadmin'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100"><?php echo e(__('Superadmin')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($user->department); ?></td>
                                    <?php if(auth()->user()->isSuperadmin()): ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($user->hospital ? $user->hospital->name : '-'); ?></td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($user->created_at->format('d M Y')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <?php
                                                $canManage = auth()->user()->isSuperadmin() ||
                                                    (auth()->user()->hospital_id === $user->hospital_id && $user->role !== 'superadmin');
                                            ?>
                                            <a href="<?php echo e(route('users.show', $user)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" title="<?php echo e(__('View')); ?>" aria-label="<?php echo e(__('View')); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 5c-5 0-9 5-9 7s4 7 9 7 9-5 9-7-4-7-9-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9Z"/>
                                                </svg>
                                            </a>
                                            <?php if($canManage): ?>
                                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:text-indigo-400 dark:hover:bg-indigo-900" title="<?php echo e(__('Edit')); ?>" aria-label="<?php echo e(__('Edit')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08ZM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83Z"/>
                                                    </svg>
                                                </a>
                                                <?php if($user->id != Auth::id()): ?>
                                                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this user?')); ?>')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900" title="<?php echo e(__('Delete')); ?>" aria-label="<?php echo e(__('Delete')); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                                <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1Zm-3 6h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 9Zm3 2v8h2v-8H9Zm4 0v8h2v-8h-2Z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <?php echo e($users->links()); ?>

                </div>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No users found.')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/kmkb/resources/views/users/index.blade.php ENDPATH**/ ?>