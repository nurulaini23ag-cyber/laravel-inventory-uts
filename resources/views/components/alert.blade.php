@props(['type' => 'info'])

@php
  $classMap = [
    'success' => 'background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;',
    'error'  => 'background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;',
    'danger' => 'background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;',
    'warning' => 'background-color: #fffbeb; color: #92400e; border: 1px solid #fde68a;',
    'info'  => 'background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;',
  ];
  $style = $classMap[$type] ?? $classMap['info'];
@endphp

<div class="alert alert-{{ $type }}" style="{{ $style }} padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; font-weight: 500;">
  <div>{{ $slot }}</div>
</div>
