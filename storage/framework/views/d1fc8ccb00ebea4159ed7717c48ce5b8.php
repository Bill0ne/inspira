<?php if($posts->isNotEmpty()): ?>
    <?php echo Theme::partial('blog.posts', compact('posts')); ?>

<?php else: ?>
    <h1 class="text-center"><?php echo e(__('Ops! No results found')); ?></h1>
    <p class="text-center">
        <?php echo e(__('We couldn’t find what you searched for. Try searching again or')); ?>

        <a class="link-primary custom-link" href="<?php echo e(route('public.single', 'blog')); ?>"><?php echo e(__('Back here')); ?></a>
    </p>
<?php endif; ?>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/templates/posts.blade.php ENDPATH**/ ?>