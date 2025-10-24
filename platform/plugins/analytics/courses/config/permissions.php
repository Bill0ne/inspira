<?php

return [
    [
        'name' => 'Manage Courses',
        'flag' => 'manage-course.index',
    ],
    [
        'name' => 'Courses',
        'flag' => 'course.index',
        'parent_flag' => 'manage-course.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'course.create',
        'parent_flag' => 'course.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'course.edit',
        'parent_flag' => 'course.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'course.destroy',
        'parent_flag' => 'course.index',
    ],

    [
        'name' => 'Course Reviews',
        'flag' => 'course-review.index',
        'parent_flag' => 'manage-course.index',
    ],
    [
        'name' => 'Destroy',
        'flag' => 'course-review.destroy',
        'parent_flag' => 'course-review.index',
    ],

    // instructor
    [
        'name' => 'Instructors',
        'flag' => 'instructor.index',
        'parent_flag' => 'manage-course.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'instructor.create',
        'parent_flag' => 'instructor.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'instructor.edit',
        'parent_flag' => 'instructor.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'instructor.destroy',
        'parent_flag' => 'instructor.index',
    ],

    // course category
    [
        'name' => 'Course Category',
        'flag' => 'course-category.index',
        'parent_flag' => 'manage-course.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'course-category.create',
        'parent_flag' => 'course-category.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'course-category.edit',
        'parent_flag' => 'course-category.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'course-category.destroy',
        'parent_flag' => 'course-category.index',
    ],

    // course booking
    [
        'name' => 'Course Booking',
        'flag' => 'course-booking.index',
        'parent_flag' => 'manage-course.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'course-booking.create',
        'parent_flag' => 'course-category.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'course-booking.edit',
        'parent_flag' => 'course-category.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'course-booking.destroy',
        'parent_flag' => 'course-category.index',
    ],

    [
        'name' => 'Course Sessions',
        'flag' => 'course-session.index',
        'parent_flag' => 'manage-course.index',
    ],
];
