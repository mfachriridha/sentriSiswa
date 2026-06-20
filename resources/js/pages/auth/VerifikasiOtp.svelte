<script module lang="ts">
    export const layout = {
        title: 'Verifikasi OTP',
        description: 'Masukkan kode verifikasi yang dikirim ke email Anda',
    };
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import InputError from '@/components/InputError.svelte';

    let {
        tujuan = '',
        sisaPercobaan = 3,
    }: {
        tujuan?: string;
        sisaPercobaan?: number;
    } = $props();

    let form = useForm({
        otp: '',
    });

    let otpInputs: HTMLInputElement[] = [];

    function handleOtpInput(index: number, e: Event) {
        const input = e.target as HTMLInputElement;
        const value = input.value;
        
        if (value.length === 1 && index < 3) {
            otpInputs[index + 1]?.focus();
        }
        
        updateOtpValue();
    }

    function handleOtpKeydown(index: number, e: KeyboardEvent) {
        if (e.key === 'Backspace' && !form.otp[index] && index > 0) {
            otpInputs[index - 1]?.focus();
        }
    }

    function handlePaste(e: ClipboardEvent) {
        e.preventDefault();
        const pastedData = e.clipboardData?.getData('text') || '';
        const digits = pastedData.replace(/\D/g, '').slice(0, 4);
        
        if (digits.length === 4) {
            form.otp = digits;
            digits.split('').forEach((digit, i) => {
                if (otpInputs[i]) {
                    otpInputs[i].value = digit;
                }
            });
            otpInputs[3]?.focus();
        }
    }

    function updateOtpValue() {
        form.otp = otpInputs.map(input => input.value).join('');
    }

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/otp/verifikasi');
    }

    const tujuanLabel = tujuan === 'ganti_email' ? 'pergantian email' : 'pergantian kata sandi';
</script>

<AppHead title="Verifikasi OTP" />

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight">Verifikasi OTP</CardTitle>
        <CardDescription>
            Masukkan kode 4 digit yang dikirim ke email Anda untuk {tujuanLabel}
        </CardDescription>
    </CardHeader>
    <CardContent>
        <form onsubmit={handleSubmit} class="space-y-6">
            <div class="space-y-2">
                <Label>Kode Verifikasi</Label>
                <div class="flex justify-center gap-3">
                    {#each [0, 1, 2, 3] as i}
                        <Input
                            bind:this={otpInputs[i]}
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]"
                            maxlength="1"
                            class="h-14 w-14 text-center text-2xl font-bold"
                            oninput={(e) => handleOtpInput(i, e)}
                            onkeydown={(e) => handleOtpKeydown(i, e)}
                            onpaste={handlePaste}
                            autocomplete="one-time-code"
                        />
                    {/each}
                </div>
                <InputError message={form.errors.otp} class="mt-2 text-center" />
            </div>

            {#if sisaPercobaan < 3}
                <p class="text-center text-sm text-muted-foreground">
                    Sisa percobaan: {sisaPercobaan}
                </p>
            {/if}

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Verifikasi
            </Button>

            <div class="text-center">
                <button
                    type="button"
                    class="text-sm text-primary hover:underline"
                    onclick={() => {
                        const formData = new FormData();
                        formData.append('tujuan', tujuan);
                        fetch('/otp/kirim', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }).then(() => {
                            window.location.reload();
                        });
                    }}
                >
                    Kirim ulang kode
                </button>
            </div>
        </form>
    </CardContent>
</Card>
