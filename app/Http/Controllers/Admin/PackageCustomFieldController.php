<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Package\AddPackageCustomFieldAction;
use App\Actions\Package\RemovePackageCustomFieldAction;
use App\Models\Package;
use App\Models\PackageCustomField;
use Illuminate\Http\Request;

class PackageCustomFieldController extends Controller
{
    public function store(Request $request, Package $package)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'type' => ['required', 'in:text,email,number,textarea,select,checkbox,file'],
            'placeholder' => ['nullable', 'string', 'max:150'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'string'],
            'is_required' => ['boolean'],
            'validation_rules' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        app(AddPackageCustomFieldAction::class)($package, $data);

        return redirect()->route('business.packages.edit', $package)->with('status', 'Custom field added.');
    }

    public function destroy(Request $request, Package $package, PackageCustomField $field)
    {
        app(RemovePackageCustomFieldAction::class)($package, $field);

        return redirect()->route('business.packages.edit', $package)->with('status', 'Custom field removed.');
    }
}
