<x-core::button tag="a" :href="route('course.duplicate', $course->getKey())" icon="ti ti-copy">
    {{ trans('core/acl::permissions.duplicate') }}
</x-core::button>
