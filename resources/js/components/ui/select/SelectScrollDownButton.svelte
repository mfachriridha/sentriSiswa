<script lang="ts">
	import { ChevronDown as ChevronDownIcon } from '@lucide/svelte';
	import { Select as SelectPrimitive } from 'bits-ui';
	import { cn } from '@/lib/utils';
	import type { ComponentProps } from 'svelte';

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: ComponentProps<typeof SelectPrimitive.ScrollDownButton> = $props();
</script>

<SelectPrimitive.ScrollDownButton
	bind:ref
	data-slot="select-scroll-down-button"
	class={cn("flex cursor-default items-center justify-center py-1", className)}
	{...restProps}
>
	<ChevronDownIcon class="size-4" />
</SelectPrimitive.ScrollDownButton>
