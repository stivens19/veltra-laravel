<?php

namespace Veltra\VeltraLaravel\Commands\Concerns;

trait HasReferenceComponentTemplates
{
    /**
     * Retorna datos estructurados completos para catalog.json.
     */
    protected function getCatalogData(): array
    {
        return [
            'name' => 'VeltraUI Enterprise Component Catalog',
            'version' => '1.2.0',
            'accent' => '#06b6d4',
            'stacks_supported' => ['blade', 'livewire', 'react', 'vue', 'svelte'],
            'components' => [
                'atoms' => [
                    'Button' => ['variants' => ['cyan', 'secondary', 'outline', 'danger', 'ghost'], 'sizes' => ['sm', 'md', 'lg']],
                    'Badge' => ['variants' => ['cyan', 'emerald', 'amber', 'rose', 'slate'], 'sizes' => ['sm', 'md']],
                    'Input' => ['types' => ['text', 'email', 'password', 'number', 'search']],
                    'Textarea' => ['props' => ['rows', 'placeholder', 'hasError']],
                    'Select' => ['props' => ['options', 'hasError', 'placeholder']],
                    'Checkbox' => ['props' => ['label', 'description', 'checked']],
                    'Toggle' => ['props' => ['checked', 'label', 'disabled']],
                    'Avatar' => ['sizes' => ['xs', 'sm', 'md', 'lg', 'xl'], 'props' => ['src', 'name', 'online']],
                    'ThemeToggle' => ['modes' => ['light', 'dark', 'system']],
                    'Spinner' => ['sizes' => ['sm', 'md', 'lg']],
                ],
                'molecules' => [
                    'FormField' => ['props' => ['label', 'name', 'error', 'required', 'hint']],
                    'StatCard' => ['props' => ['title', 'value', 'icon', 'trend', 'trendUp']],
                    'Alert' => ['variants' => ['info', 'success', 'warning', 'danger'], 'props' => ['title', 'dismissible']],
                    'SearchInput' => ['props' => ['placeholder', 'model', 'shortcut']],
                    'UserPanel' => ['props' => ['user', 'role', 'status']],
                    'Tabs' => ['props' => ['tabs', 'activeTab', 'onChange']],
                    'Breadcrumb' => ['props' => ['items']],
                    'Pagination' => ['props' => ['currentPage', 'totalPages', 'onPageChange']],
                    'Dropdown' => ['props' => ['trigger', 'items']],
                ],
                'organisms' => [
                    'Card' => ['slots' => ['header', 'default', 'footer', 'actions']],
                    'Table' => ['features' => ['striped', 'hover', 'responsive', 'pagination', 'selection']],
                    'Modal' => ['props' => ['id', 'title', 'open', 'size']],
                    'Accordion' => ['props' => ['items', 'multiple']],
                    'Sidebar' => ['features' => ['collapsible', 'navigation_tree', 'brand_header']],
                    'Header' => ['features' => ['theme_toggle', 'user_menu', 'search']],
                ],
            ],
        ];
    }

    /**
     * 1. Button
     */
    protected function getButtonCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';
import { LucideIcon } from 'lucide-react';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: 'cyan' | 'secondary' | 'outline' | 'danger' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    icon?: LucideIcon;
    children: React.ReactNode;
}

export const Button: React.FC<ButtonProps> = ({
    variant = 'cyan',
    size = 'md',
    icon: Icon,
    children,
    className = '',
    ...props
}) => {
    const variants = {
        cyan: 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-md shadow-cyan-500/20 active:scale-[0.98]',
        secondary: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200',
        outline: 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300',
        danger: 'bg-rose-500 hover:bg-rose-600 text-white shadow-xs',
        ghost: 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400',
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-xs',
        md: 'px-4 py-2 text-xs font-semibold',
        lg: 'px-5 py-2.5 text-sm font-bold',
    };

    return (
        <button
            className={`inline-flex items-center justify-center gap-2 rounded-xl transition-all duration-200 cursor-pointer disabled:opacity-50 ${variants[variant]} ${sizes[size]} ${className}`}
            {...props}
        >
            {Icon && <Icon className="w-4 h-4 shrink-0" />}
            <span>{children}</span>
        </button>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    variant?: 'cyan' | 'secondary' | 'outline' | 'danger' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    icon?: any;
}>(), {
    variant: 'cyan',
    size: 'md',
});

const variantClasses = computed(() => ({
    cyan: 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-md shadow-cyan-500/20 active:scale-[0.98]',
    secondary: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200',
    outline: 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300',
    danger: 'bg-rose-500 hover:bg-rose-600 text-white shadow-xs',
    ghost: 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400',
}[props.variant]));

const sizeClasses = computed(() => ({
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2 text-xs font-semibold',
    lg: 'px-5 py-2.5 text-sm font-bold',
}[props.size]));
</script>

<template>
  <button :class="['inline-flex items-center justify-center gap-2 rounded-xl transition-all duration-200 cursor-pointer disabled:opacity-50', variantClasses, sizeClasses]">
    <component :is="icon" v-if="icon" class="w-4 h-4 shrink-0" />
    <slot />
  </button>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  import type { ComponentType } from 'svelte';
  export let variant: 'cyan' | 'secondary' | 'outline' | 'danger' | 'ghost' = 'cyan';
  export let size: 'sm' | 'md' | 'lg' = 'md';
  export let icon: ComponentType | undefined = undefined;

  const variants = {
    cyan: 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-md shadow-cyan-500/20 active:scale-[0.98]',
    secondary: 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200',
    outline: 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300',
    danger: 'bg-rose-500 hover:bg-rose-600 text-white shadow-xs',
    ghost: 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400',
  };

  const sizes = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2 text-xs font-semibold',
    lg: 'px-5 py-2.5 text-sm font-bold',
  };
</script>

<button
  class="inline-flex items-center justify-center gap-2 rounded-xl transition-all duration-200 cursor-pointer disabled:opacity-50 {variants[variant]} {sizes[size]}"
  {...$$restProps}
>
  {#if icon}
    <svelte:component this={icon} class="w-4 h-4 shrink-0" />
  {/if}
  <slot />
</button>
SVELTE,
            default => <<<'BLADE'
@props([
    'variant' => 'cyan',
    'size' => 'md',
    'icon' => null,
])

@php
$variants = [
    'cyan' => 'bg-cyan-500 hover:bg-cyan-600 text-white shadow-md shadow-cyan-500/20 active:scale-[0.98]',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200',
    'outline' => 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300',
    'danger' => 'bg-rose-500 hover:bg-rose-600 text-white shadow-xs',
    'ghost' => 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-xs font-semibold',
    'lg' => 'px-5 py-2.5 text-sm font-bold',
];

$classes = ($variants[$variant] ?? $variants['cyan']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl transition-all duration-200 cursor-pointer disabled:opacity-50 {$classes}"]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
    @endif
    {{ $slot }}
</button>
BLADE,
        };
    }

    /**
     * 2. Badge
     */
    protected function getBadgeCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface BadgeProps {
    variant?: 'cyan' | 'emerald' | 'amber' | 'rose' | 'slate';
    children: React.ReactNode;
}

export const Badge: React.FC<BadgeProps> = ({ variant = 'cyan', children }) => {
    const variants = {
        cyan: 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border-cyan-200/50 dark:border-cyan-800/40',
        emerald: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/40',
        amber: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/40',
        rose: 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-800/40',
        slate: 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
    };

    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${variants[variant]}`}>
            {children}
        </span>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    variant?: 'cyan' | 'emerald' | 'amber' | 'rose' | 'slate';
}>(), {
    variant: 'cyan',
});

const variantClass = computed(() => ({
    cyan: 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border-cyan-200/50 dark:border-cyan-800/40',
    emerald: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/40',
    amber: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/40',
    rose: 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-800/40',
    slate: 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
}[props.variant]));
</script>

<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border', variantClass]">
    <slot />
  </span>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let variant: 'cyan' | 'emerald' | 'amber' | 'rose' | 'slate' = 'cyan';

  const variants = {
    cyan: 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border-cyan-200/50 dark:border-cyan-800/40',
    emerald: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/40',
    amber: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/40',
    rose: 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-800/40',
    slate: 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
  };
</script>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {variants[variant]}">
  <slot />
</span>
SVELTE,
            default => <<<'BLADE'
@props(['variant' => 'cyan'])

@php
$variants = [
    'cyan' => 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 border-cyan-200/50 dark:border-cyan-800/40',
    'emerald' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/40',
    'amber' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/40',
    'rose' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-800/40',
    'slate' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
];
$classes = $variants[$variant] ?? $variants['cyan'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {$classes}"]) }}>
    {{ $slot }}
</span>
BLADE,
        };
    }

    /**
     * 3. Input
     */
    protected function getInputCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    hasError?: boolean;
}

export const Input: React.FC<InputProps> = ({ hasError = false, className = '', ...props }) => (
    <input
        className={`w-full px-3.5 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors ${
            hasError
                ? 'border-rose-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500'
                : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500'
        } ${className}`}
        {...props}
    />
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{
    hasError?: boolean;
}>();
</script>

<template>
  <input
    :class="[
      'w-full px-3.5 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors',
      hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500'
    ]"
  />
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let hasError: boolean = false;
</script>

<input
  class="w-full px-3.5 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors {hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500'}"
  {...$$restProps}
/>
SVELTE,
            default => <<<'BLADE'
@props(['hasError' => false])

<input {{ $attributes->merge([
    'class' => 'w-full px-3.5 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors ' .
        ($hasError ? 'border-rose-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500')
]) }}>
BLADE,
        };
    }

    /**
     * 4. Textarea
     */
    protected function getTextareaCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
    hasError?: boolean;
}

export const Textarea: React.FC<TextareaProps> = ({ hasError = false, className = '', ...props }) => (
    <textarea
        className={`w-full px-3.5 py-2.5 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors resize-y min-h-[90px] ${
            hasError
                ? 'border-rose-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500'
                : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500'
        } ${className}`}
        {...props}
    />
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ hasError?: boolean }>();
</script>
<template>
  <textarea
    :class="[
      'w-full px-3.5 py-2.5 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors resize-y min-h-[90px]',
      hasError ? 'border-rose-500 focus:border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500'
    ]"
  />
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let hasError: boolean = false;
</script>
<textarea
  class="w-full px-3.5 py-2.5 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors resize-y min-h-[90px] {hasError ? 'border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500'}"
  {...$$restProps}
></textarea>
SVELTE,
            default => <<<'BLADE'
@props(['hasError' => false])

<textarea {{ $attributes->merge([
    'class' => 'w-full px-3.5 py-2.5 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors resize-y min-h-[90px] ' .
        ($hasError ? 'border-rose-500 focus:border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500')
]) }}>{{ $slot }}</textarea>
BLADE,
        };
    }

    /**
     * 5. Select
     */
    protected function getSelectCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
    options?: { value: string | number; label: string }[];
    hasError?: boolean;
    children?: React.ReactNode;
}

export const Select: React.FC<SelectProps> = ({ options, hasError = false, children, className = '', ...props }) => (
    <div className="relative">
        <select
            className={`w-full appearance-none pl-3.5 pr-9 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none transition-colors cursor-pointer ${
                hasError
                    ? 'border-rose-500 focus:border-rose-500'
                    : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500'
            } ${className}`}
            {...props}
        >
            {options ? options.map((opt) => <option key={opt.value} value={opt.value}>{opt.label}</option>) : children}
        </select>
        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeWidth="2" strokeLinecap="round" d="m6 9 6 6 6-6"/></svg>
        </div>
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{
    options?: { value: string | number; label: string }[];
    hasError?: boolean;
}>();
</script>
<template>
  <div class="relative">
    <select :class="['w-full appearance-none pl-3.5 pr-9 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none cursor-pointer', hasError ? 'border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500']">
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
      <slot />
    </select>
    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeWidth="2" strokeLinecap="round" d="m6 9 6 6 6-6"/></svg>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let options: { value: string | number; label: string }[] = [];
  export let hasError: boolean = false;
</script>
<div class="relative">
  <select class="w-full appearance-none pl-3.5 pr-9 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none cursor-pointer {hasError ? 'border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500'}" {...$$restProps}>
    {#each options as opt}
      <option value={opt.value}>{opt.label}</option>
    {/each}
    <slot />
  </select>
  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
  </div>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['options' => [], 'hasError' => false])

<div class="relative">
    <select {{ $attributes->merge([
        'class' => 'w-full appearance-none pl-3.5 pr-9 py-2 text-xs rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none cursor-pointer ' .
            ($hasError ? 'border-rose-500' : 'border-slate-200 dark:border-slate-800 focus:border-cyan-500')
    ]) }}>
        @foreach($options as $val => $lbl)
            <option value="{{ $val }}">{{ $lbl }}</option>
        @endforeach
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
    </div>
</div>
BLADE,
        };
    }

    /**
     * 6. Checkbox
     */
    protected function getCheckboxCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface CheckboxProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'type'> {
    label?: string;
    description?: string;
}

export const Checkbox: React.FC<CheckboxProps> = ({ label, description, className = '', ...props }) => (
    <label className="flex items-start gap-2.5 cursor-pointer select-none">
        <input
            type="checkbox"
            className={`w-4 h-4 mt-0.5 rounded-md border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500 focus:ring-offset-0 bg-white dark:bg-slate-900 ${className}`}
            {...props}
        />
        {(label || description) && (
            <div>
                {label && <span className="text-xs font-semibold text-slate-800 dark:text-slate-200 block">{label}</span>}
                {description && <span className="text-[11px] text-slate-500 dark:text-slate-400 block">{description}</span>}
            </div>
        )}
    </label>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ label?: string; description?: string }>();
</script>
<template>
  <label class="flex items-start gap-2.5 cursor-pointer select-none">
    <input type="checkbox" class="w-4 h-4 mt-0.5 rounded-md border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500 bg-white dark:bg-slate-900" />
    <div v-if="label || description">
      <span v-if="label" class="text-xs font-semibold text-slate-800 dark:text-slate-200 block">{{ label }}</span>
      <span v-if="description" class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ description }}</span>
    </div>
  </label>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let label: string = '';
  export let description: string = '';
</script>
<label class="flex items-start gap-2.5 cursor-pointer select-none">
  <input type="checkbox" class="w-4 h-4 mt-0.5 rounded-md border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500 bg-white dark:bg-slate-900" {...$$restProps} />
  {#if label || description}
    <div>
      {#if label}<span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block">{label}</span>{/if}
      {#if description}<span class="text-[11px] text-slate-500 dark:text-slate-400 block">{description}</span>{/if}
    </div>
  {/if}
</label>
SVELTE,
            default => <<<'BLADE'
@props(['label' => null, 'description' => null])

<label class="flex items-start gap-2.5 cursor-pointer select-none">
    <input type="checkbox" {{ $attributes->merge(['class' => 'w-4 h-4 mt-0.5 rounded-md border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500 bg-white dark:bg-slate-900']) }}>
    @if($label || $description)
        <div>
            @if($label)<span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block">{{ $label }}</span>@endif
            @if($description)<span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $description }}</span>@endif
        </div>
    @endif
</label>
BLADE,
        };
    }

    /**
     * 7. Toggle (Switch)
     */
    protected function getToggleCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface ToggleProps {
    checked: boolean;
    onChange: (checked: boolean) => void;
    label?: string;
    disabled?: boolean;
}

export const Toggle: React.FC<ToggleProps> = ({ checked, onChange, label, disabled = false }) => (
    <label className="inline-flex items-center gap-3 cursor-pointer select-none">
        <button
            type="button"
            role="switch"
            aria-checked={checked}
            disabled={disabled}
            onClick={() => !disabled && onChange(!checked)}
            className={`relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-cyan-500/20 disabled:opacity-50 ${
                checked ? 'bg-cyan-500' : 'bg-slate-200 dark:bg-slate-800'
            }`}
        >
            <span className={`pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out ${
                checked ? 'translate-x-5' : 'translate-x-0'
            }`} />
        </button>
        {label && <span className="text-xs font-semibold text-slate-800 dark:text-slate-200">{label}</span>}
    </label>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ modelValue: boolean; label?: string; disabled?: boolean }>();
defineEmits(['update:modelValue']);
</script>
<template>
  <label class="inline-flex items-center gap-3 cursor-pointer select-none">
    <button
      type="button"
      role="switch"
      :aria-checked="modelValue"
      :disabled="disabled"
      @click="!disabled && $emit('update:modelValue', !modelValue)"
      :class="['relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200', modelValue ? 'bg-cyan-500' : 'bg-slate-200 dark:bg-slate-800']"
    >
      <span :class="['pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200', modelValue ? 'translate-x-5' : 'translate-x-0']" />
    </button>
    <span v-if="label" class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ label }}</span>
  </label>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let checked: boolean = false;
  export let label: string = '';
  export let disabled: boolean = false;
</script>
<label class="inline-flex items-center gap-3 cursor-pointer select-none">
  <button
    type="button"
    role="switch"
    aria-checked={checked}
    {disabled}
    on:click={() => !disabled && (checked = !checked)}
    class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 {checked ? 'bg-cyan-500' : 'bg-slate-200 dark:bg-slate-800'}"
  >
    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200 {checked ? 'translate-x-5' : 'translate-x-0'}" />
  </button>
  {#if label}<span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{label}</span>{/if}
</label>
SVELTE,
            default => <<<'BLADE'
@props(['checked' => false, 'label' => null])

<label class="inline-flex items-center gap-3 cursor-pointer select-none">
    <input type="checkbox" {{ $attributes }} class="sr-only peer" {{ $checked ? 'checked' : '' }}>
    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500 relative"></div>
    @if($label)<span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $label }}</span>@endif
</label>
BLADE,
        };
    }

    /**
     * 8. Avatar
     */
    protected function getAvatarCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface AvatarProps {
    src?: string;
    name?: string;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    online?: boolean;
    className?: string;
}

export const Avatar: React.FC<AvatarProps> = ({ src, name = 'U', size = 'md', online, className = '' }) => {
    const sizes = {
        xs: 'w-6 h-6 text-[10px] rounded-lg',
        sm: 'w-8 h-8 text-xs rounded-xl',
        md: 'w-10 h-10 text-sm rounded-xl',
        lg: 'w-12 h-12 text-base rounded-2xl',
        xl: 'w-16 h-16 text-xl rounded-2xl',
    };
    const initials = name ? name.substring(0, 2).toUpperCase() : 'U';

    return (
        <div className="relative inline-block shrink-0">
            {src ? (
                <img src={src} alt={name} className={`object-cover ${sizes[size]} ${className}`} />
            ) : (
                <div className={`bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center ring-1 ring-cyan-500/20 ${sizes[size]} ${className}`}>
                    {initials}
                </div>
            )}
            {online !== undefined && (
                <span className={`absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-white dark:border-slate-900 ${
                    online ? 'bg-emerald-500' : 'bg-slate-400'
                } ${size === 'xs' ? 'w-2 h-2' : 'w-3 h-3'}`} />
            )}
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    src?: string;
    name?: string;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    online?: boolean;
}>(), {
    name: 'U',
    size: 'md',
});

const sizeClasses = computed(() => ({
    xs: 'w-6 h-6 text-[10px] rounded-lg',
    sm: 'w-8 h-8 text-xs rounded-xl',
    md: 'w-10 h-10 text-sm rounded-xl',
    lg: 'w-12 h-12 text-base rounded-2xl',
    xl: 'w-16 h-16 text-xl rounded-2xl',
}[props.size]));

const initials = computed(() => (props.name ? props.name.substring(0, 2).toUpperCase() : 'U'));
</script>

<template>
  <div class="relative inline-block shrink-0">
    <img v-if="src" :src="src" :alt="name" :class="['object-cover', sizeClasses]" />
    <div v-else :class="['bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center ring-1 ring-cyan-500/20', sizeClasses]">
      {{ initials }}
    </div>
    <span v-if="online !== undefined" :class="['absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-white dark:border-slate-900', online ? 'bg-emerald-500' : 'bg-slate-400', size === 'xs' ? 'w-2 h-2' : 'w-3 h-3']" />
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let src: string | undefined = undefined;
  export let name: string = 'U';
  export let size: 'xs' | 'sm' | 'md' | 'lg' | 'xl' = 'md';
  export let online: boolean | undefined = undefined;

  const sizes = {
    xs: 'w-6 h-6 text-[10px] rounded-lg',
    sm: 'w-8 h-8 text-xs rounded-xl',
    md: 'w-10 h-10 text-sm rounded-xl',
    lg: 'w-12 h-12 text-base rounded-2xl',
    xl: 'w-16 h-16 text-xl rounded-2xl',
  };

  $: initials = name ? name.substring(0, 2).toUpperCase() : 'U';
</script>

<div class="relative inline-block shrink-0">
  {#if src}
    <img {src} alt={name} class="object-cover {sizes[size]}" />
  {:else}
    <div class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center ring-1 ring-cyan-500/20 {sizes[size]}">
      {initials}
    </div>
  {/if}
  {#if online !== undefined}
    <span class="absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-white dark:border-slate-900 {online ? 'bg-emerald-500' : 'bg-slate-400'} {size === 'xs' ? 'w-2 h-2' : 'w-3 h-3'}"></span>
  {/if}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['src' => null, 'name' => 'U', 'size' => 'md', 'online' => null])

@php
$sizes = [
    'xs' => 'w-6 h-6 text-[10px] rounded-lg',
    'sm' => 'w-8 h-8 text-xs rounded-xl',
    'md' => 'w-10 h-10 text-sm rounded-xl',
    'lg' => 'w-12 h-12 text-base rounded-2xl',
    'xl' => 'w-16 h-16 text-xl rounded-2xl',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$initials = strtoupper(substr($name ?: 'U', 0, 2));
@endphp

<div class="relative inline-block shrink-0">
    @if($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="object-cover {{ $sizeClass }}">
    @else
        <div class="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center ring-1 ring-cyan-500/20 {{ $sizeClass }}">
            {{ $initials }}
        </div>
    @endif
    @if($online !== null)
        <span class="absolute -bottom-0.5 -right-0.5 rounded-full border-2 border-white dark:border-slate-900 {{ $online ? 'bg-emerald-500' : 'bg-slate-400' }} {{ $size === 'xs' ? 'w-2 h-2' : 'w-3 h-3' }}"></span>
    @endif
</div>
BLADE,
        };
    }

    /**
     * 9. ThemeToggle
     */
    protected function getThemeToggleCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React, { useState, useEffect } from 'react';

export const ThemeToggle: React.FC<{ className?: string }> = ({ className = '' }) => {
    const [isDark, setIsDark] = useState(false);

    useEffect(() => {
        const checkTheme = () => {
            const current = document.documentElement.getAttribute('data-theme') ||
                (document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            setIsDark(current === 'dark');
        };
        checkTheme();
        const obs = new MutationObserver(checkTheme);
        obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
        return () => obs.disconnect();
    }, []);

    const toggle = () => {
        const next = isDark ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        document.documentElement.classList.toggle('dark', next === 'dark');
        try {
            localStorage.setItem('veltra-theme', next);
            localStorage.setItem('appearance', next);
            document.cookie = `appearance=${next};path=/;max-age=31536000;SameSite=Lax`;
        } catch (e) {}
        setIsDark(!isDark);
    };

    return (
        <button
            type="button"
            onClick={toggle}
            aria-label="Toggle theme"
            className={`w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/50 dark:border-slate-700/50 cursor-pointer transition-colors ${className}`}
        >
            {isDark ? (
                <svg className="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" strokeWidth="2"/><path strokeWidth="2" strokeLinecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14-1.41 1.41"/></svg>
            ) : (
                <svg className="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            )}
        </button>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const isDark = ref(false);
let observer: MutationObserver | null = null;

onMounted(() => {
    const check = () => {
        isDark.value = (document.documentElement.getAttribute('data-theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light')) === 'dark';
    };
    check();
    observer = new MutationObserver(check);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
});

onUnmounted(() => observer?.disconnect());

const toggle = () => {
    const next = isDark.value ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    document.documentElement.classList.toggle('dark', next === 'dark');
    try {
        localStorage.setItem('veltra-theme', next);
        localStorage.setItem('appearance', next);
        document.cookie = `appearance=${next};path=/;max-age=31536000;SameSite=Lax`;
    } catch (e) {}
    isDark.value = !isDark.value;
};
</script>
<template>
  <button type="button" @click="toggle" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/50 dark:border-slate-700/50 cursor-pointer">
    <span v-if="isDark" class="text-amber-400">☀</span>
    <span v-else class="text-cyan-600">🌙</span>
  </button>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  import { onMount } from 'svelte';
  let isDark = false;

  onMount(() => {
    const check = () => {
      isDark = (document.documentElement.getAttribute('data-theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light')) === 'dark';
    };
    check();
    const obs = new MutationObserver(check);
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
    return () => obs.disconnect();
  });

  const toggle = () => {
    const next = isDark ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    document.documentElement.classList.toggle('dark', next === 'dark');
    try {
      localStorage.setItem('veltra-theme', next);
      localStorage.setItem('appearance', next);
      document.cookie = `appearance=${next};path=/;max-age=31536000;SameSite=Lax`;
    } catch (e) {}
    isDark = !isDark;
  };
</script>
<button type="button" on:click={toggle} class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/50 dark:border-slate-700/50 cursor-pointer">
  {isDark ? '☀' : '🌙'}
</button>
SVELTE,
            default => <<<'BLADE'
<button type="button" onclick="(function(){ const d=document.documentElement; const n=d.classList.contains('dark')?'light':'dark'; d.setAttribute('data-theme',n); d.classList.toggle('dark',n==='dark'); localStorage.setItem('veltra-theme',n); localStorage.setItem('appearance',n); document.cookie='appearance='+n+';path=/;max-age=31536000;SameSite=Lax'; })()" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/50 dark:border-slate-700/50 cursor-pointer">
    <svg class="w-4 h-4 text-cyan-600 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    <svg class="w-4 h-4 text-amber-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14-1.41 1.41"/></svg>
</button>
BLADE,
        };
    }

    /**
     * 10. Spinner
     */
    protected function getSpinnerCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

export const Spinner: React.FC<{ size?: 'sm' | 'md' | 'lg'; className?: string }> = ({ size = 'md', className = '' }) => {
    const sizes = { sm: 'w-4 h-4', md: 'w-6 h-6', lg: 'w-8 h-8' };
    return (
        <svg className={`animate-spin text-cyan-500 ${sizes[size]} ${className}`} fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ size?: 'sm' | 'md' | 'lg' }>();
</script>
<template>
  <svg :class="['animate-spin text-cyan-500', size === 'sm' ? 'w-4 h-4' : (size === 'lg' ? 'w-8 h-8' : 'w-6 h-6')]" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
  </svg>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let size: 'sm' | 'md' | 'lg' = 'md';
  const sizes = { sm: 'w-4 h-4', md: 'w-6 h-6', lg: 'w-8 h-8' };
</script>
<svg class="animate-spin text-cyan-500 {sizes[size]}" fill="none" viewBox="0 0 24 24">
  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
</svg>
SVELTE,
            default => <<<'BLADE'
@props(['size' => 'md'])

@php
$sizes = ['sm' => 'w-4 h-4', 'md' => 'w-6 h-6', 'lg' => 'w-8 h-8'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<svg {{ $attributes->merge(['class' => "animate-spin text-cyan-500 {$sizeClass}"]) }} fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
</svg>
BLADE,
        };
    }

    /**
     * 11. FormField
     */
    protected function getFormFieldCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface FormFieldProps {
    label: string;
    name?: string;
    error?: string;
    hint?: string;
    required?: boolean;
    children: React.ReactNode;
}

export const FormField: React.FC<FormFieldProps> = ({
    label,
    name,
    error,
    hint,
    required = false,
    children,
}) => (
    <div className="space-y-1.5 w-full">
        <label htmlFor={name} className="block text-xs font-bold text-slate-700 dark:text-slate-300">
            {label} {required && <span className="text-rose-500">*</span>}
        </label>
        {children}
        {hint && !error && <p className="text-[11px] text-slate-500 dark:text-slate-400">{hint}</p>}
        {error && <p className="text-[11px] font-semibold text-rose-500">{error}</p>}
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ label: string; name?: string; error?: string; hint?: string; required?: boolean }>();
</script>
<template>
  <div class="space-y-1.5 w-full">
    <label :for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
      {{ label }} <span v-if="required" class="text-rose-500">*</span>
    </label>
    <slot />
    <p v-if="hint && !error" class="text-[11px] text-slate-500 dark:text-slate-400">{{ hint }}</p>
    <p v-if="error" class="text-[11px] font-semibold text-rose-500">{{ error }}</p>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let label: string;
  export let name: string = '';
  export let error: string = '';
  export let hint: string = '';
  export let required: boolean = false;
</script>
<div class="space-y-1.5 w-full">
  <label for={name} class="block text-xs font-bold text-slate-700 dark:text-slate-300">
    {label} {#if required}<span class="text-rose-500">*</span>{/if}
  </label>
  <slot />
  {#if hint && !error}<p class="text-[11px] text-slate-500 dark:text-slate-400">{hint}</p>{/if}
  {#if error}<p class="text-[11px] font-semibold text-rose-500">{error}</p>{/if}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['label', 'name' => null, 'error' => null, 'hint' => null, 'required' => false])

<div class="space-y-1.5 w-full">
    <label @if($name) for="{{ $name }}" @endif class="block text-xs font-bold text-slate-700 dark:text-slate-300">
        {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    {{ $slot }}
    @if($hint && !$error)<p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $hint }}</p>@endif
    @if($error)<p class="text-[11px] font-semibold text-rose-500">{{ $error }}</p>@endif
</div>
BLADE,
        };
    }

    /**
     * 12. StatCard
     */
    protected function getStatCardCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';
import { LucideIcon } from 'lucide-react';

interface StatCardProps {
    title: string;
    value: string | number;
    icon: LucideIcon;
    trend?: string;
    trendUp?: boolean;
}

export const StatCard: React.FC<StatCardProps> = ({
    title,
    value,
    icon: Icon,
    trend,
    trendUp = true,
}) => (
    <div className="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs">
        <div className="flex items-center justify-between">
            <span className="text-xs font-semibold text-slate-500 dark:text-slate-400">{title}</span>
            <div className="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                <Icon className="w-5 h-5" />
            </div>
        </div>
        <div className="mt-3">
            <h3 className="text-2xl font-black text-slate-900 dark:text-white font-mono">{value}</h3>
            {trend && (
                <p className={`text-xs mt-1 flex items-center gap-1 font-semibold ${trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}`}>
                    <span>{trendUp ? '▲' : '▼'} {trend}</span>
                </p>
            )}
        </div>
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ title: string; value: string | number; icon: any; trend?: string; trendUp?: boolean }>();
</script>
<template>
  <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ title }}</span>
      <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
        <component :is="icon" class="w-5 h-5" />
      </div>
    </div>
    <div class="mt-3">
      <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ value }}</h3>
      <p v-if="trend" :class="['text-xs mt-1 font-semibold', trendUp !== false ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
        {{ trendUp !== false ? '▲' : '▼' }} {{ trend }}
      </p>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  import type { ComponentType } from 'svelte';
  export let title: string;
  export let value: string | number;
  export let icon: ComponentType;
  export let trend: string = '';
  export let trendUp: boolean = true;
</script>
<div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs">
  <div class="flex items-center justify-between">
    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{title}</span>
    <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
      <svelte:component this={icon} class="w-5 h-5" />
    </div>
  </div>
  <div class="mt-3">
    <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono">{value}</h3>
    {#if trend}
      <p class="text-xs mt-1 font-semibold {trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}">
        {trendUp ? '▲' : '▼'} {trend}
      </p>
    {/if}
  </div>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['title', 'value', 'icon' => 'activity', 'trend' => null, 'trendUp' => true])

<div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs">
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $title }}</span>
        <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
    </div>
    <div class="mt-3">
        <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $value }}</h3>
        @if($trend)
            <p class="text-xs mt-1 font-semibold {{ $trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                <span>{{ $trendUp ? '▲' : '▼' }} {{ $trend }}</span>
            </p>
        @endif
    </div>
</div>
BLADE,
        };
    }

    /**
     * 13. Alert
     */
    protected function getAlertCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React, { useState } from 'react';

interface AlertProps {
    variant?: 'info' | 'success' | 'warning' | 'danger';
    title?: string;
    children: React.ReactNode;
    dismissible?: boolean;
}

export const Alert: React.FC<AlertProps> = ({ variant = 'info', title, children, dismissible = true }) => {
    const [visible, setVisible] = useState(true);
    if (!visible) return null;

    const variants = {
        info: 'bg-cyan-50/70 dark:bg-cyan-950/30 text-cyan-800 dark:text-cyan-200 border-cyan-200 dark:border-cyan-800/40',
        success: 'bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800/40',
        warning: 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200 border-amber-200 dark:border-amber-800/40',
        danger: 'bg-rose-50/70 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-900/40',
    };

    return (
        <div className={`p-4 rounded-2xl border text-xs flex items-start justify-between gap-3 ${variants[variant]}`}>
            <div>
                {title && <h4 className="font-bold mb-0.5">{title}</h4>}
                <div className="leading-relaxed">{children}</div>
            </div>
            {dismissible && (
                <button type="button" onClick={() => setVisible(false)} className="p-1 rounded-lg opacity-60 hover:opacity-100 transition-opacity cursor-pointer">
                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeWidth="2" strokeLinecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            )}
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { ref } from 'vue';

const props = withDefaults(defineProps<{
    variant?: 'info' | 'success' | 'warning' | 'danger';
    title?: string;
    dismissible?: boolean;
}>(), {
    variant: 'info',
    dismissible: true,
});

const visible = ref(true);
</script>
<template>
  <div v-if="visible" :class="['p-4 rounded-2xl border text-xs flex items-start justify-between gap-3', {
    'bg-cyan-50/70 dark:bg-cyan-950/30 text-cyan-800 dark:text-cyan-200 border-cyan-200 dark:border-cyan-800/40': variant === 'info',
    'bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800/40': variant === 'success',
    'bg-amber-50/70 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200 border-amber-200 dark:border-amber-800/40': variant === 'warning',
    'bg-rose-50/70 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-900/40': variant === 'danger',
  }]">
    <div>
      <h4 v-if="title" class="font-bold mb-0.5">{{ title }}</h4>
      <div class="leading-relaxed"><slot /></div>
    </div>
    <button v-if="dismissible" type="button" @click="visible = false" class="p-1 rounded-lg opacity-60 hover:opacity-100 cursor-pointer">✕</button>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let variant: 'info' | 'success' | 'warning' | 'danger' = 'info';
  export let title: string = '';
  export let dismissible: boolean = true;
  let visible = true;

  const variants = {
    info: 'bg-cyan-50/70 dark:bg-cyan-950/30 text-cyan-800 dark:text-cyan-200 border-cyan-200 dark:border-cyan-800/40',
    success: 'bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800/40',
    warning: 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200 border-amber-200 dark:border-amber-800/40',
    danger: 'bg-rose-50/70 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-900/40',
  };
</script>
{#if visible}
  <div class="p-4 rounded-2xl border text-xs flex items-start justify-between gap-3 {variants[variant]}">
    <div>
      {#if title}<h4 class="font-bold mb-0.5">{title}</h4>{/if}
      <div class="leading-relaxed"><slot /></div>
    </div>
    {#if dismissible}
      <button type="button" on:click={() => visible = false} class="p-1 rounded-lg opacity-60 hover:opacity-100 cursor-pointer">✕</button>
    {/if}
  </div>
{/if}
SVELTE,
            default => <<<'BLADE'
@props(['variant' => 'info', 'title' => null, 'dismissible' => true])

@php
$variants = [
    'info' => 'bg-cyan-50/70 dark:bg-cyan-950/30 text-cyan-800 dark:text-cyan-200 border-cyan-200 dark:border-cyan-800/40',
    'success' => 'bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800/40',
    'warning' => 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200 border-amber-200 dark:border-amber-800/40',
    'danger' => 'bg-rose-50/70 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-900/40',
];
$classes = $variants[$variant] ?? $variants['info'];
@endphp

<div x-data="{ visible: true }" x-show="visible" {{ $attributes->merge(['class' => "p-4 rounded-2xl border text-xs flex items-start justify-between gap-3 {$classes}"]) }}>
    <div>
        @if($title)<h4 class="font-bold mb-0.5">{{ $title }}</h4>@endif
        <div class="leading-relaxed">{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" @click="visible = false" class="p-1 rounded-lg opacity-60 hover:opacity-100 cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
BLADE,
        };
    }

    /**
     * 14. SearchInput
     */
    protected function getSearchInputCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface SearchInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    shortcut?: string;
}

export const SearchInput: React.FC<SearchInputProps> = ({ shortcut = '⌘K', className = '', ...props }) => (
    <div className="relative w-full">
        <svg className="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z" />
        </svg>
        <input
            type="search"
            placeholder="Buscar..."
            className={`w-full pl-9 pr-12 py-2 text-xs rounded-xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 focus:border-cyan-500 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors ${className}`}
            {...props}
        />
        {shortcut && (
            <kbd className="absolute right-2.5 top-2 px-1.5 py-0.5 text-[10px] font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md text-slate-400 shadow-2xs">
                {shortcut}
            </kbd>
        )}
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ shortcut?: string }>();
</script>
<template>
  <div class="relative w-full">
    <span class="absolute left-3 top-2 text-slate-400">🔍</span>
    <input type="search" placeholder="Buscar..." class="w-full pl-9 pr-12 py-2 text-xs rounded-xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 focus:border-cyan-500 text-slate-900 dark:text-white focus:outline-none" />
    <kbd v-if="shortcut" class="absolute right-2.5 top-2 px-1.5 py-0.5 text-[10px] font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md text-slate-400">{{ shortcut }}</kbd>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let shortcut: string = '⌘K';
</script>
<div class="relative w-full">
  <span class="absolute left-3 top-2 text-slate-400">🔍</span>
  <input type="search" placeholder="Buscar..." class="w-full pl-9 pr-12 py-2 text-xs rounded-xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 focus:border-cyan-500 text-slate-900 dark:text-white focus:outline-none" {...$$restProps} />
  {#if shortcut}<kbd class="absolute right-2.5 top-2 px-1.5 py-0.5 text-[10px] font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md text-slate-400">{shortcut}</kbd>{/if}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['shortcut' => '⌘K'])

<div class="relative w-full">
    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
    <input type="search" {{ $attributes->merge(['class' => 'w-full pl-9 pr-12 py-2 text-xs rounded-xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 focus:border-cyan-500 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none transition-colors']) }}>
    @if($shortcut)
        <kbd class="absolute right-2.5 top-2 px-1.5 py-0.5 text-[10px] font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md text-slate-400 shadow-2xs">{{ $shortcut }}</kbd>
    @endif
</div>
BLADE,
        };
    }

    /**
     * 15. UserPanel
     */
    protected function getUserPanelCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface UserPanelProps {
    user: { name?: string; email?: string; avatar?: string };
    role?: string;
}

export const UserPanel: React.FC<UserPanelProps> = ({ user, role = 'Administrator' }) => {
    const initials = user?.name ? user.name.substring(0, 2).toUpperCase() : 'U';
    return (
        <div className="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
            <div className="relative shrink-0">
                <div className="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-xs ring-1 ring-cyan-500/20">
                    {initials}
                </div>
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-800 absolute -bottom-0.5 -right-0.5" />
            </div>
            <div className="min-w-0 flex-1">
                <p className="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{user?.name || 'Usuario'}</p>
                <p className="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{role}</p>
            </div>
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ user: { name?: string; email?: string }; role?: string }>();
</script>
<template>
  <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-xs">
      {{ user?.name ? user.name.substring(0,2).toUpperCase() : 'U' }}
    </div>
    <div class="min-w-0 flex-1">
      <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ user?.name || 'Usuario' }}</p>
      <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ role || 'Administrator' }}</p>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let user: { name?: string; email?: string } = {};
  export let role: string = 'Administrator';
  $: initials = user?.name ? user.name.substring(0, 2).toUpperCase() : 'U';
</script>
<div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
  <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-xs">{initials}</div>
  <div class="min-w-0 flex-1">
    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{user?.name || 'Usuario'}</p>
    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{role}</p>
  </div>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['user', 'role' => 'Administrator'])

@php $initials = strtoupper(substr($user->name ?? 'U', 0, 2)); @endphp

<div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
    <div class="relative shrink-0">
        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-xs ring-1 ring-cyan-500/20">
            {{ $initials }}
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-800 absolute -bottom-0.5 -right-0.5"></span>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $user->name ?? 'Usuario' }}</p>
        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $role }}</p>
    </div>
</div>
BLADE,
        };
    }

    /**
     * 16. Tabs
     */
    protected function getTabsCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface TabItem {
    id: string;
    label: string;
    count?: number;
}

interface TabsProps {
    tabs: TabItem[];
    activeTab: string;
    onChange: (id: string) => void;
}

export const Tabs: React.FC<TabsProps> = ({ tabs, activeTab, onChange }) => (
    <div className="flex items-center gap-1.5 border-b border-slate-200/80 dark:border-slate-800 pb-2">
        {tabs.map((tab) => {
            const isActive = tab.id === activeTab;
            return (
                <button
                    key={tab.id}
                    type="button"
                    onClick={() => onChange(tab.id)}
                    className={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                        isActive
                            ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60'
                    }`}
                >
                    <span>{tab.label}</span>
                    {tab.count !== undefined && (
                        <span className={`px-1.5 py-0.2 rounded-full text-[10px] ${
                            isActive ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'
                        }`}>
                            {tab.count}
                        </span>
                    )}
                </button>
            );
        })}
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ tabs: { id: string; label: string; count?: number }[]; activeTab: string }>();
defineEmits(['change']);
</script>
<template>
  <div class="flex items-center gap-1.5 border-b border-slate-200/80 dark:border-slate-800 pb-2">
    <button
      v-for="t in tabs"
      :key="t.id"
      type="button"
      @click="$emit('change', t.id)"
      :class="['inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold cursor-pointer', t.id === activeTab ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60']"
    >
      <span>{{ t.label }}</span>
      <span v-if="t.count !== undefined" class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700">{{ t.count }}</span>
    </button>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let tabs: { id: string; label: string; count?: number }[] = [];
  export let activeTab: string = '';
</script>
<div class="flex items-center gap-1.5 border-b border-slate-200/80 dark:border-slate-800 pb-2">
  {#each tabs as t}
    <button
      type="button"
      on:click={() => activeTab = t.id}
      class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold cursor-pointer {t.id === activeTab ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60'}"
    >
      <span>{t.label}</span>
      {#if t.count !== undefined}<span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700">{t.count}</span>{/if}
    </button>
  {/each}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['tabs' => [], 'activeTab' => ''])

<div class="flex items-center gap-1.5 border-b border-slate-200/80 dark:border-slate-800 pb-2">
    @foreach($tabs as $tab)
        @php $isActive = ($tab['id'] ?? '') === $activeTab; @endphp
        <button type="button" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $isActive ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
            <span>{{ $tab['label'] ?? '' }}</span>
            @if(isset($tab['count']))
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">{{ $tab['count'] }}</span>
            @endif
        </button>
    @endforeach
</div>
BLADE,
        };
    }

    /**
     * 17. Breadcrumb
     */
    protected function getBreadcrumbCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface BreadcrumbItem {
    label: string;
    href?: string;
}

export const Breadcrumb: React.FC<{ items: BreadcrumbItem[] }> = ({ items }) => (
    <nav className="flex items-center gap-2 text-xs" aria-label="Breadcrumb">
        <ol className="flex items-center gap-2">
            {items.map((item, index) => {
                const isLast = index === items.length - 1;
                return (
                    <React.Fragment key={index}>
                        {index > 0 && <span className="text-slate-300 dark:text-slate-600">/</span>}
                        {isLast || !item.href ? (
                            <span className="font-bold text-cyan-600 dark:text-cyan-400">{item.label}</span>
                        ) : (
                            <a href={item.href} className="text-slate-500 hover:text-cyan-600 dark:text-slate-400 dark:hover:text-cyan-400 transition-colors">
                                {item.label}
                            </a>
                        )}
                    </React.Fragment>
                );
            })}
        </ol>
    </nav>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ items: { label: string; href?: string }[] }>();
</script>
<template>
  <nav class="flex items-center gap-2 text-xs" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2">
      <template v-for="(item, i) in items" :key="i">
        <span v-if="i > 0" class="text-slate-300 dark:text-slate-600">/</span>
        <span v-if="i === items.length - 1 || !item.href" class="font-bold text-cyan-600 dark:text-cyan-400">{{ item.label }}</span>
        <a v-else :href="item.href" class="text-slate-500 hover:text-cyan-600 dark:text-slate-400">{{ item.label }}</a>
      </template>
    </ol>
  </nav>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let items: { label: string; href?: string }[] = [];
</script>
<nav class="flex items-center gap-2 text-xs" aria-label="Breadcrumb">
  <ol class="flex items-center gap-2">
    {#each items as item, i}
      {#if i > 0}<span class="text-slate-300 dark:text-slate-600">/</span>{/if}
      {#if i === items.length - 1 || !item.href}
        <span class="font-bold text-cyan-600 dark:text-cyan-400">{item.label}</span>
      {:else}
        <a href={item.href} class="text-slate-500 hover:text-cyan-600 dark:text-slate-400">{item.label}</a>
      {/if}
    {/each}
  </ol>
</nav>
SVELTE,
            default => <<<'BLADE'
@props(['items' => []])

<nav class="flex items-center gap-2 text-xs" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2">
        @foreach($items as $i => $item)
            @if($i > 0)<span class="text-slate-300 dark:text-slate-600">/</span>@endif
            @if($loop->last || empty($item['href']))
                <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ $item['label'] ?? '' }}</span>
            @else
                <a href="{{ $item['href'] }}" class="text-slate-500 hover:text-cyan-600 dark:text-slate-400 transition-colors">{{ $item['label'] ?? '' }}</a>
            @endif
        @endforeach
    </ol>
</nav>
BLADE,
        };
    }

    /**
     * 18. Pagination
     */
    protected function getPaginationCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface PaginationProps {
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
}

export const Pagination: React.FC<PaginationProps> = ({ currentPage, totalPages, onPageChange }) => {
    const pages = Array.from({ length: totalPages }, (_, i) => i + 1);
    return (
        <div className="flex items-center justify-between py-3 text-xs">
            <span className="text-slate-500 dark:text-slate-400">Página {currentPage} de {totalPages}</span>
            <div className="flex items-center gap-1">
                <button
                    type="button"
                    disabled={currentPage <= 1}
                    onClick={() => onPageChange(currentPage - 1)}
                    className="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                >
                    Anterior
                </button>
                {pages.map((p) => (
                    <button
                        key={p}
                        type="button"
                        onClick={() => onPageChange(p)}
                        className={`w-8 h-8 rounded-xl font-bold cursor-pointer ${
                            p === currentPage
                                ? 'bg-cyan-500 text-white shadow-xs'
                                : 'border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300'
                        }`}
                    >
                        {p}
                    </button>
                ))}
                <button
                    type="button"
                    disabled={currentPage >= totalPages}
                    onClick={() => onPageChange(currentPage + 1)}
                    className="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                >
                    Siguiente
                </button>
            </div>
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ currentPage: number; totalPages: number }>();
defineEmits(['change']);
</script>
<template>
  <div class="flex items-center justify-between py-3 text-xs">
    <span class="text-slate-500 dark:text-slate-400">Página {{ currentPage }} de {{ totalPages }}</span>
    <div class="flex items-center gap-1">
      <button :disabled="currentPage <= 1" @click="$emit('change', currentPage - 1)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 cursor-pointer">Anterior</button>
      <button v-for="p in totalPages" :key="p" @click="$emit('change', p)" :class="['w-8 h-8 rounded-xl font-bold cursor-pointer', p === currentPage ? 'bg-cyan-500 text-white' : 'border border-slate-200 dark:border-slate-800']">{{ p }}</button>
      <button :disabled="currentPage >= totalPages" @click="$emit('change', currentPage + 1)" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 cursor-pointer">Siguiente</button>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let currentPage: number = 1;
  export let totalPages: number = 1;
</script>
<div class="flex items-center justify-between py-3 text-xs">
  <span class="text-slate-500 dark:text-slate-400">Página {currentPage} de {totalPages}</span>
  <div class="flex items-center gap-1">
    <button disabled={currentPage <= 1} on:click={() => currentPage--} class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 cursor-pointer">Anterior</button>
    <button class="w-8 h-8 rounded-xl font-bold bg-cyan-500 text-white cursor-pointer">{currentPage}</button>
    <button disabled={currentPage >= totalPages} on:click={() => currentPage++} class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-40 cursor-pointer">Siguiente</button>
  </div>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['paginator'])

<div class="py-3">
    {{ $paginator->links() }}
</div>
BLADE,
        };
    }

    /**
     * 19. Dropdown
     */
    protected function getDropdownCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React, { useState, useRef, useEffect } from 'react';

interface DropdownItem {
    label: string;
    href?: string;
    onClick?: () => void;
    danger?: boolean;
    icon?: React.ReactNode;
}

export const Dropdown: React.FC<{ trigger: React.ReactNode; items: DropdownItem[] }> = ({ trigger, items }) => {
    const [open, setOpen] = useState(false);
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <div className="relative inline-block" ref={ref}>
            <div onClick={() => setOpen(!open)} className="cursor-pointer">
                {trigger}
            </div>
            {open && (
                <div className="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50 animate-in fade-in zoom-in-95 duration-150">
                    {items.map((item, i) => (
                        <button
                            key={i}
                            type="button"
                            onClick={() => { item.onClick?.(); setOpen(false); }}
                            className={`w-full text-left px-4 py-2 text-xs flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer ${
                                item.danger ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300'
                            }`}
                        >
                            {item.icon}
                            <span>{item.label}</span>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { ref } from 'vue';
defineProps<{ items: { label: string; danger?: boolean }[] }>();
const open = ref(false);
</script>
<template>
  <div class="relative inline-block">
    <div @click="open = !open" class="cursor-pointer"><slot name="trigger" /></div>
    <div v-if="open" class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50">
      <button v-for="(it, i) in items" :key="i" type="button" :class="['w-full text-left px-4 py-2 text-xs flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800', it.danger ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300']">{{ it.label }}</button>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let items: { label: string; danger?: boolean }[] = [];
  let open = false;
</script>
<div class="relative inline-block">
  <div on:click={() => open = !open} class="cursor-pointer"><slot name="trigger" /></div>
  {#if open}
    <div class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50">
      {#each items as it}
        <button type="button" class="w-full text-left px-4 py-2 text-xs flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 {it.danger ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300'}">{it.label}</button>
      {/each}
    </div>
  {/if}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['items' => []])

<div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block">
    <div @click="open = !open" class="cursor-pointer">
        {{ $trigger ?? $slot }}
    </div>
    <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1 z-50">
        @foreach($items as $item)
            <a href="{{ $item['href'] ?? '#' }}" class="block px-4 py-2 text-xs {{ !empty($item['danger']) ? 'text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                {{ $item['label'] ?? '' }}
            </a>
        @endforeach
    </div>
</div>
BLADE,
        };
    }

    /**
     * 20. Card
     */
    protected function getCardCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface CardProps {
    title?: string;
    subtitle?: string;
    actions?: React.ReactNode;
    footer?: React.ReactNode;
    children: React.ReactNode;
    className?: string;
}

export const Card: React.FC<CardProps> = ({
    title,
    subtitle,
    actions,
    footer,
    children,
    className = '',
}) => (
    <div className={`vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden ${className}`}>
        {(title || actions) && (
            <div className="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                <div>
                    {title && <h2 className="text-sm font-bold text-slate-900 dark:text-white">{title}</h2>}
                    {subtitle && <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{subtitle}</p>}
                </div>
                {actions && <div className="flex items-center gap-2">{actions}</div>}
            </div>
        )}
        <div className="p-6">
            {children}
        </div>
        {footer && (
            <div className="px-6 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 text-xs">
                {footer}
            </div>
        )}
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ title?: string; subtitle?: string }>();
</script>
<template>
  <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden">
    <div v-if="title || $slots.actions" class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
      <div>
        <h2 v-if="title" class="text-sm font-bold text-slate-900 dark:text-white">{{ title }}</h2>
        <p v-if="subtitle" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ subtitle }}</p>
      </div>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
    <div class="p-6"><slot /></div>
    <div v-if="$slots.footer" class="px-6 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 text-xs">
      <slot name="footer" />
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let title: string = '';
  export let subtitle: string = '';
</script>
<div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden">
  {#if title}
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
      <div>
        <h2 class="text-sm font-bold text-slate-900 dark:text-white">{title}</h2>
        {#if subtitle}<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{subtitle}</p>{/if}
      </div>
      <slot name="actions" />
    </div>
  {/if}
  <div class="p-6"><slot /></div>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl shadow-xs overflow-hidden']) }}>
    @if($title || isset($actions))
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
            <div>
                @if($title)<h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $title }}</h2>@endif
                @if($subtitle)<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            @if(isset($actions))<div class="flex items-center gap-2">{{ $actions }}</div>@endif
        </div>
    @endif
    <div class="p-6">{{ $slot }}</div>
    @if(isset($footer))
        <div class="px-6 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 text-xs">{{ $footer }}</div>
    @endif
</div>
BLADE,
        };
    }

    /**
     * 21. Table
     */
    protected function getTableCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface TableProps {
    headers: string[];
    children: React.ReactNode;
}

export const Table: React.FC<TableProps> = ({ headers, children }) => (
    <div className="overflow-x-auto w-full">
        <table className="w-full text-left text-xs text-slate-700 dark:text-slate-200">
            <thead className="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                <tr>
                    {headers.map((h, i) => (
                        <th key={i} className="py-3 px-5">{h}</th>
                    ))}
                </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                {children}
            </tbody>
        </table>
    </div>
);
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ headers: string[] }>();
</script>
<template>
  <div class="overflow-x-auto w-full">
    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
      <thead class="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
        <tr><th v-for="(h, i) in headers" :key="i" class="py-3 px-5">{{ h }}</th></tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-800"><slot /></tbody>
    </table>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let headers: string[] = [];
</script>
<div class="overflow-x-auto w-full">
  <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
    <thead class="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
      <tr>{#each headers as h}<th class="py-3 px-5">{h}</th>{/each}</tr>
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-800"><slot /></tbody>
  </table>
</div>
SVELTE,
            default => <<<'BLADE'
@props(['headers' => []])

<div class="overflow-x-auto w-full">
    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
        <thead class="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
            <tr>
                @foreach($headers as $h)
                    <th class="py-3 px-5">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">{{ $slot }}</tbody>
    </table>
</div>
BLADE,
        };
    }

    /**
     * 22. Modal
     */
    protected function getModalCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React from 'react';

interface ModalProps {
    open: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
    footer?: React.ReactNode;
}

export const Modal: React.FC<ModalProps> = ({ open, onClose, title, children, footer }) => {
    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div className="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" onClick={onClose} />
            <div className="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-200">
                <div className="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <h3 className="text-sm font-bold text-slate-900 dark:text-white">{title}</h3>
                    <button type="button" onClick={onClose} className="p-1 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeWidth="2" strokeLinecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div className="text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    {children}
                </div>
                {footer && (
                    <div className="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                        {footer}
                    </div>
                )}
            </div>
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
defineProps<{ open: boolean; title: string }>();
defineEmits(['close']);
</script>
<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs" @click="$emit('close')" />
    <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xl z-10">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ title }}</h3>
        <button type="button" @click="$emit('close')" class="p-1 text-slate-400 hover:text-slate-200 cursor-pointer">✕</button>
      </div>
      <div class="text-xs text-slate-600 dark:text-slate-300"><slot /></div>
      <div v-if="$slots.footer" class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2"><slot name="footer" /></div>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let open: boolean = false;
  export let title: string = '';
</script>
{#if open}
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs" on:click={() => open = false}></div>
    <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xl z-10">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{title}</h3>
        <button type="button" on:click={() => open = false} class="p-1 text-slate-400 hover:text-slate-200 cursor-pointer">✕</button>
      </div>
      <div class="text-xs text-slate-600 dark:text-slate-300"><slot /></div>
    </div>
  </div>
{/if}
SVELTE,
            default => <<<'BLADE'
@props(['name', 'title' => '', 'show' => false])

<div
    x-data="{ show: @js($show) }"
    x-show="show"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs" @click="show = false"></div>
    <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
            <button type="button" @click="show = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="text-xs text-slate-600 dark:text-slate-300">
            {{ $slot }}
        </div>
        @if(isset($footer))
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
BLADE,
        };
    }

    /**
     * 23. Accordion
     */
    protected function getAccordionCode(string $stack): string
    {
        return match ($stack) {
            'react' => <<<'TSX'
import React, { useState } from 'react';

interface AccordionItem {
    id: string;
    title: string;
    content: React.ReactNode;
}

export const Accordion: React.FC<{ items: AccordionItem[]; multiple?: boolean }> = ({ items, multiple = false }) => {
    const [openIds, setOpenIds] = useState<string[]>([]);

    const toggle = (id: string) => {
        if (multiple) {
            setOpenIds((prev) => prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]);
        } else {
            setOpenIds((prev) => prev.includes(id) ? [] : [id]);
        }
    };

    return (
        <div className="space-y-2 w-full">
            {items.map((item) => {
                const isOpen = openIds.includes(item.id);
                return (
                    <div key={item.id} className="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl overflow-hidden">
                        <button
                            type="button"
                            onClick={() => toggle(item.id)}
                            className="w-full flex items-center justify-between px-5 py-3.5 text-xs font-bold text-slate-900 dark:text-white cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
                        >
                            <span>{item.title}</span>
                            <svg className={`w-4 h-4 text-slate-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeWidth="2" strokeLinecap="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        {isOpen && (
                            <div className="px-5 pb-4 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/60 pt-3 leading-relaxed">
                                {item.content}
                            </div>
                        )}
                    </div>
                );
            })}
        </div>
    );
};
TSX,
            'vue' => <<<'VUE'
<script setup lang="ts">
import { ref } from 'vue';
defineProps<{ items: { id: string; title: string; content: string }[] }>();
const active = ref<string | null>(null);
</script>
<template>
  <div class="space-y-2 w-full">
    <div v-for="it in items" :key="it.id" class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl overflow-hidden">
      <button type="button" @click="active = active === it.id ? null : it.id" class="w-full flex items-center justify-between px-5 py-3.5 text-xs font-bold text-slate-900 dark:text-white cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40">
        <span>{{ it.title }}</span>
        <span :class="['transition-transform duration-200', active === it.id ? 'rotate-180' : '']">▼</span>
      </button>
      <div v-if="active === it.id" class="px-5 pb-4 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/60 pt-3">
        {{ it.content }}
      </div>
    </div>
  </div>
</template>
VUE,
            'svelte' => <<<'SVELTE'
<script lang="ts">
  export let items: { id: string; title: string; content: string }[] = [];
  let activeId: string | null = null;
</script>
<div class="space-y-2 w-full">
  {#each items as it}
    <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl overflow-hidden">
      <button type="button" on:click={() => activeId = activeId === it.id ? null : it.id} class="w-full flex items-center justify-between px-5 py-3.5 text-xs font-bold text-slate-900 dark:text-white cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40">
        <span>{it.title}</span>
        <span class="transition-transform duration-200 {activeId === it.id ? 'rotate-180' : ''}">▼</span>
      </button>
      {#if activeId === it.id}
        <div class="px-5 pb-4 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/60 pt-3">
          {it.content}
        </div>
      {/if}
    </div>
  {/each}
</div>
SVELTE,
            default => <<<'BLADE'
@props(['items' => []])

<div x-data="{ active: null }" class="space-y-2 w-full">
    @foreach($items as $item)
        <div class="vlt-card border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl overflow-hidden">
            <button type="button" @click="active = active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}'" class="w-full flex items-center justify-between px-5 py-3.5 text-xs font-bold text-slate-900 dark:text-white cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                <span>{{ $item['title'] ?? '' }}</span>
                <svg :class="active === '{{ $item['id'] }}' ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="m19 9-7 7-7-7"/></svg>
            </button>
            <div x-show="active === '{{ $item['id'] }}'" class="px-5 pb-4 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/60 pt-3 leading-relaxed">
                {{ $item['content'] ?? '' }}
            </div>
        </div>
    @endforeach
</div>
BLADE,
        };
    }
}
