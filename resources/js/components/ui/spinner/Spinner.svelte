<script lang="ts">
    import { cn } from '@/lib/utils';
    import { LoaderCircle } from '@lucide/svelte';

    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<LoaderCircle class={cn('animate-spin', className)} {...rest} />
