<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VacationsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\FileShareController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollResponseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BusinessEventController;
use App\Http\Controllers\ChatController;


Route::get('/',  [VacationsController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users
    Route::get('users', [AdminUserController::class, 'index'])->middleware('permission:view_users')->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->middleware('permission:create_users')->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->middleware('permission:create_users')->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->middleware('permission:edit_users')->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->middleware('permission:edit_users')->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:delete_users')->name('users.destroy');

    // Departments
    Route::get('departments', [DepartmentController::class, 'index'])->middleware('permission:view_departments')->name('departments.index');
    Route::get('departments/create', [DepartmentController::class, 'create'])->middleware('permission:create_departments')->name('departments.create');
    Route::post('departments', [DepartmentController::class, 'store'])->middleware('permission:create_departments')->name('departments.store');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->middleware('permission:edit_departments')->name('departments.edit');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->middleware('permission:edit_departments')->name('departments.update');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:delete_departments')->name('departments.destroy');

    // Poll Management
    Route::get('polls', [PollController::class, 'index'])->middleware('permission:view_polls')->name('polls.index');
    Route::get('polls/create', [PollController::class, 'create'])->middleware('permission:create_polls')->name('polls.create');
    Route::post('polls', [PollController::class, 'store'])->middleware('permission:create_polls')->name('polls.store');
    Route::get('polls/{poll}', [PollController::class, 'show'])->middleware('permission:view_polls')->name('polls.show');
    Route::get('polls/{poll}/edit', [PollController::class, 'edit'])->middleware('permission:create_polls')->name('polls.edit');
    Route::put('polls/{poll}', [PollController::class, 'update'])->middleware('permission:create_polls')->name('polls.update');
    Route::delete('polls/{poll}', [PollController::class, 'destroy'])->middleware('permission:create_polls')->name('polls.destroy');

    // Roles
    Route::middleware('permission:manage_roles')->resource('roles', RoleController::class)->except(['show']);

    // Meetings
    Route::get('meetings', [MeetingController::class, 'adminIndex'])->middleware('permission:create_meetings')->name('meetings.index');
    Route::get('meetings/create', [MeetingController::class, 'create'])->middleware('permission:create_meetings')->name('meetings.create');
    Route::post('meetings', [MeetingController::class, 'store'])->middleware('permission:create_meetings')->name('meetings.store');
    Route::delete('meetings/{meeting}', [MeetingController::class, 'destroy'])->middleware('permission:delete_meetings')->name('meetings.destroy');

    // Platform Settings
    Route::middleware('permission:manage_platform_settings')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});


Route::middleware('auth')->group(function () {
    Route::middleware('permission:view_polls')->get('/polls', [PollResponseController::class, 'index'])->name('polls.index');
    Route::middleware('permission:view_polls')->get('/polls/{poll}', [PollResponseController::class, 'show'])->name('polls.show');
    Route::middleware('permission:vote_polls')->post('/polls/{poll}', [PollResponseController::class, 'store'])->name('polls.store');
    
    Route::get('/profile/{user?}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.upload-image');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->middleware('permission:view_announcements')->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->middleware('permission:create_announcements')->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->middleware('permission:edit_announcements')->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->middleware('permission:edit_announcements')->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('permission:delete_announcements')->name('announcements.destroy');
    Route::post('comments', [CommentController::class, 'store'])->middleware('permission:view_announcements')->name('comments.store');
    
    // Chat Routes
    Route::middleware('permission:access_chat')->group(function () {
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/chat/general', [ChatController::class, 'getGeneralMessages'])->name('chat.general');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->middleware('throttle:60,1')->name('chat.send');
        Route::get('/chat/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    });

    // File Share Routes
    Route::middleware('permission:view_files')->group(function () {
        Route::get('/files', [FileShareController::class, 'index'])->name('files.index');
        Route::get('/files/create', [FileShareController::class, 'create'])->name('files.create');
        Route::get('/files/{id}/download', [FileShareController::class, 'download'])->name('files.download');
    });
    Route::middleware('permission:upload_files')->group(function () {
        Route::post('/files/folder', [FileShareController::class, 'storeFolder'])->name('files.storeFolder');
        Route::post('/files', [FileShareController::class, 'store'])->name('files.store');
    });
    Route::middleware('permission:delete_files')->group(function () {
        Route::delete('/files/{id}', [FileShareController::class, 'destroy'])->name('files.destroy');
        Route::post('/files/{id}/move', [FileShareController::class, 'move'])->name('files.move');
    });

    // Business Calendar Routes
    Route::get('/calendar', [BusinessEventController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [BusinessEventController::class, 'getEvents'])->name('calendar.events');
    
    // Meeting Routes
    Route::middleware('permission:view_meetings')->get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');

    Route::middleware('admin')->group(function () {
        Route::post('/calendar/events', [BusinessEventController::class, 'store'])->name('calendar.store');
        Route::put('/calendar/events/{event}', [BusinessEventController::class, 'update'])->name('calendar.update');
        Route::delete('/calendar/events/{event}', [BusinessEventController::class, 'destroy'])->name('calendar.destroy');
    });

    // Vacations
    Route::middleware('permission:view_vacations')->get('/vacations', [VacationsController::class, 'index'])->name('vacations');
    Route::middleware('permission:create_vacations')->post('/vacations/add', [VacationsController::class, 'add'])->name('vacations.add');
    Route::middleware('permission:approve_vacations')->post('/vacations/{vacation}/verify', [VacationsController::class, 'verify'])->name('vacations.verify');
    Route::middleware('permission:approve_vacations')->post('/vacations/{vacation}/reject', [VacationsController::class, 'reject'])->name('vacations.reject');

    // Tasks
    Route::resource('tasks', TaskController::class)->only(['index', 'show'])->middleware('permission:view_tasks');
    Route::resource('tasks', TaskController::class)->only(['create', 'store'])->middleware('permission:create_tasks');
    Route::resource('tasks', TaskController::class)->only(['edit', 'update'])->middleware('permission:edit_tasks');
    Route::resource('tasks', TaskController::class)->only(['destroy'])->middleware('permission:delete_tasks');
});

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead')->middleware('auth');

require __DIR__.'/auth.php';
