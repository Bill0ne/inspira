<?php if($paginator->hasPages()): ?>
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <ul class="pagination custom-pagination">
                    
                    <?php if($paginator->onFirstPage()): ?>
                        <li
                            class="page-item disabled"
                            aria-disabled="true"
                            aria-label="<?php echo app('translator')->get('pagination.previous'); ?>"
                        >
                            <span
                                class="page-link page-link-navigation"
                                aria-hidden="true"
                            >&lsaquo;</span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a
                                class="page-link"
                                href="<?php echo e($paginator->previousPageUrl()); ?>"
                                aria-label="<?php echo app('translator')->get('pagination.previous'); ?>"
                                rel="prev"
                            >&lsaquo;</a>
                        </li>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(is_string($element)): ?>
                            <li
                                class="page-item disabled"
                                aria-disabled="true"
                            ><span class="page-link"><?php echo e($element); ?></span></li>
                        <?php endif; ?>

                        
                        <?php if(is_array($element)): ?>
                            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $paginator->currentPage()): ?>
                                    <li
                                        class="page-item active"
                                        aria-current="page"
                                    ><span class="page-link"><?php echo e($page); ?></span></li>
                                <?php else: ?>
                                    <li class="page-item"><a
                                            class="page-link"
                                            href="<?php echo e($url); ?>"
                                        ><?php echo e($page); ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <li class="page-item">
                            <a
                                class="page-link"
                                href="<?php echo e($paginator->nextPageUrl()); ?>"
                                aria-label="<?php echo app('translator')->get('pagination.next'); ?>"
                                rel="next"
                            >&rsaquo;</a>
                        </li>
                    <?php else: ?>
                        <li
                            class="page-item disabled"
                            aria-disabled="true"
                            aria-label="<?php echo app('translator')->get('pagination.next'); ?>"
                        >
                            <span
                                class="page-link page-link-navigation"
                                aria-hidden="true"
                            >&rsaquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/plugins/hotel/resources/views/themes/partials/pagination.blade.php ENDPATH**/ ?>