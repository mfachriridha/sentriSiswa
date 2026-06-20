<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class OtpGenerator
{
    private const OTP_LENGTH = 4;

    private const OTP_TTL_MINUTES = 5;

    private const MAX_ATTEMPTS = 3;

    private const LOCKOUT_MINUTES = 10;

    public function generate(int $userId): string
    {
        $otp = str_pad((string) random_int(0, 9999), self::OTP_LENGTH, '0', STR_PAD_LEFT);

        Cache::put($this->cacheKey($userId), $otp, now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put($this->attemptsKey($userId), 0, now()->addMinutes(self::OTP_TTL_MINUTES));

        return $otp;
    }

    public function verify(int $userId, string $inputOtp): bool
    {
        $attempts = (int) Cache::get($this->attemptsKey($userId), 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $storedOtp = Cache::get($this->cacheKey($userId));

        if ($storedOtp === null || $storedOtp !== $inputOtp) {
            Cache::put($this->attemptsKey($userId), $attempts + 1, now()->addMinutes(self::OTP_TTL_MINUTES));

            return false;
        }

        $this->clear($userId);

        return true;
    }

    public function clear(int $userId): void
    {
        Cache::forget($this->cacheKey($userId));
        Cache::forget($this->attemptsKey($userId));
    }

    public function isValid(int $userId): bool
    {
        return Cache::has($this->cacheKey($userId));
    }

    public function isLockedOut(int $userId): bool
    {
        $attempts = (int) Cache::get($this->attemptsKey($userId), 0);

        return $attempts >= self::MAX_ATTEMPTS;
    }

    public function remainingAttempts(int $userId): int
    {
        $attempts = (int) Cache::get($this->attemptsKey($userId), 0);

        return max(0, self::MAX_ATTEMPTS - $attempts);
    }

    public function throttleKey(int $userId): string
    {
        return "otp_throttle:{$userId}";
    }

    public function hitThrottle(int $userId): void
    {
        RateLimiter::hit($this->throttleKey($userId), self::LOCKOUT_MINUTES * 60);
    }

    public function tooManyRequests(int $userId): bool
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($userId), 3);
    }

    private function cacheKey(int $userId): string
    {
        return "otp_code:{$userId}";
    }

    private function attemptsKey(int $userId): string
    {
        return "otp_attempts:{$userId}";
    }
}
