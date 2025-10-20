<?php
    Theme::set('pageTitle', $post->name);
?>
<section class="inner-blog b-details-p pt-80 pb-40">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="blog-details-wrap">
                    <div class="details__content pb-30">
                        <h2><?php echo e($post->name); ?></h2>
                        <div class="meta-info">
                            <ul>
                                <li><i class="fal fa-eye"></i><?php echo e(number_format($post->views)); ?></li>
                                <li><i class="fal fa-calendar-alt"></i><?php echo e($post->created_at->translatedFormat('M d, Y')); ?></li>
                            </ul>
                        </div>
                        <div class="ck-content">
                            <?php echo BaseHelper::clean($post->content); ?>

                        </div>
                        <?php if($post->tags->isNotEmpty()): ?>
                            <div class="row">
                                <div class="col-xl-12 col-md-12">
                                    <div class="post__tag">
                                        <h5><?php echo e(__('Related Tags')); ?></h5>
                                        <ul>
                                            <?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <a href="<?php echo e($tag->url); ?>"><?php echo e($tag->name); ?></a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if(($posts = get_related_posts($post->id, 2)) && $posts->isNotEmpty()): ?>
                        <div class="posts_navigation pt-35 pb-100">
                            <div class="row align-items-center">
                                <?php if($prevPost = $posts[0]): ?>
                                    <div class="col-xl-4 col-md-5">
                                        <div class="prev-link">
                                            <span><?php echo e(__('Prev Post')); ?></span>
                                            <h4><a href="<?php echo e($prevPost->url); ?>"><?php echo e($prevPost->name); ?></a></h4>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($post->firstCategory): ?>
                                    <div class="col-xl-4 col-md-2 text-md-center">
                                        <a href="<?php echo e($post->firstCategory->url); ?>" class="blog-filter"><img src="<?php echo e(Theme::asset()->url('images/blog-category-icon.png')); ?>" alt="<?php echo e($post->firstCategory->name); ?>" /></a>
                                    </div>
                                <?php endif; ?>
                                <?php if($nextPost = (isset($posts[1]) ? $posts[1] : null)): ?>
                                    <div class="col-xl-4 col-md-5">
                                        <div class="next-link text-end text-md-right">
                                            <span><?php echo e(__('Next Post')); ?></span>
                                            <h4><a href="<?php echo e($nextPost->url); ?>"><?php echo e($nextPost->name); ?></a></h4>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mb-60"></div>
                    <?php endif; ?>

                    <?php ($author = $post->author); ?>
                    <div class="avatar__wrap text-center mb-45">
                        <div class="avatar-img">
                            <img class="author-blog-avatar" src="<?php echo e($author->avatar_url); ?>" alt="<?php echo e($author->getMetaData('display_name', true) ?: $author->name); ?>" />
                        </div>
                        <div class="avatar__info">
                            <h5><?php echo e($author->getMetaData('display_name', true) ?: $author->name); ?></h5>
                            <div class="avatar__info-social">
                                <?php $__currentLoopData = ['facebook', 'twitter', 'instagram', 'behance', 'linkedin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($url = $author->getMetaData($social, true)): ?>
                                        <?php switch($social):
                                            case ('twitter'): ?>
                                                <a href="<?php echo e($url); ?>"><i class="fab fa-twitter"></i></a>
                                                <?php break; ?>

                                            <?php case ('facebook'): ?>
                                                <a href="<?php echo e($url); ?>"><i class="fab fa-facebook-f"></i></a>
                                                <?php break; ?>

                                            <?php case ('instagram'): ?>
                                                <a href="<?php echo e($url); ?>"><i class="fab fa-instagram"></i></a>
                                                <?php break; ?>

                                            <?php case ('behance'): ?>
                                                <a href="<?php echo e($url); ?>"><i class="fab fa-behance"></i></a>
                                                <?php break; ?>

                                            <?php case ('linkedin'): ?>
                                                <a href="<?php echo e($url); ?>"><i class="fab fa-linkedin"></i></a>
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div class="avatar__wrap-content">
                            <?php echo BaseHelper::clean($author->getMetaData('bio', true)); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4">
                <aside class="sidebar-widget">
                    <?php echo dynamic_sidebar('blog_sidebar'); ?>

                </aside>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/post.blade.php ENDPATH**/ ?>