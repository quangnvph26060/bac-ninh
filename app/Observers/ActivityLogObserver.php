<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityLogObserver
{
    public function created($model)
    {
        $this->logActivity($model, 'create');
    }

    public function updated($model)
    {
        $this->logActivity($model, 'update');
    }

    public function deleted($model)
    {
        $this->logActivity($model, 'delete');
    }

    protected function logActivity($model, $action)
    {
        if (auth()->guard('admin')->check()) {
            logger(auth()->guard('admin')->id());

            // Tắt sự kiện của Eloquent để tránh vòng lặp vô hạn
            ActivityLog::withoutEvents(function () use ($model, $action) {
                ActivityLog::create([
                    'employee_id' => auth()->guard('admin')->id(),
                    'action'      => $action,
                    'model_type'  => get_class($model),
                    'model_id'    => $model->id,
                    'changes'     => $action === 'update' ? $model->getChanges() : null,
                ]);
            });
        }
    }
}
