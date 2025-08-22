<?php
// app/Http/Requests/StorePostRequest.php
public function authorize(): bool { return true; }
public function rules(): array {
  return [
    'title'       => 'required|string|max:255',
    'description' => 'required|string',
    'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
  ];
}
