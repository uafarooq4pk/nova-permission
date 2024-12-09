<?php

namespace Vyuldashev\NovaPermission;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\PermissionRegistrar;

class AttachToRole extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        // Get the Role class from the PermissionRegistrar
        $roleClass = app(PermissionRegistrar::class)->getRoleClass();

        // Use the Role class to query the database
        $role = $roleClass::find($fields['role']);

        if (!$role) {
            throw new \Exception("Role not found.");
        }

        foreach ($models as $model) {
            $role->givePermissionTo($model);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Role')->options(
                app(Role::getModel())->newQuery()->pluck('name', 'id')->toArray()
            )->displayUsingLabels(),
        ];
    }
}
