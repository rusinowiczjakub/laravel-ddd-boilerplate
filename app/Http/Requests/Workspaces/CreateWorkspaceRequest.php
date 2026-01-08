<?php

declare(strict_types=1);

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

final class CreateWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'in:free,starter,pro,enterprise'],
            'invitations' => ['nullable', 'array'],
            'invitations.*.email' => ['required', 'email'],
            'invitations.*.role' => ['required', 'string', 'in:administrator,collaborator'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'workspace name',
            'plan' => 'subscription plan',
            'invitations.*.email' => 'invitation email',
            'invitations.*.role' => 'member role',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a workspace name.',
            'name.max' => 'The workspace name cannot exceed 255 characters.',
            'invitations.*.email.required' => 'Each invitation must have an email address.',
            'invitations.*.email.email' => 'Please provide a valid email address.',
            'invitations.*.role.required' => 'Each invitation must have a role assigned.',
            'invitations.*.role.in' => 'The role must be either administrator or collaborator.',
        ];
    }
}
