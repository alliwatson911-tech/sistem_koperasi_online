# Rules: VPS Deployment, Financial Integrity, and Business Verification

## 1. VPS & aaPanel Deployment Environment Rules
- **Storage & Cache Permissions**: When deploying Laravel on Linux/aaPanel, always ensure `storage/` and `bootstrap/cache/` are recursively owned by the web server user `www:www` with permission `775`.
- **PHP `tempnam()` & `open_basedir`**: The error `tempnam(): file created in the system's temporary directory` occurs when `storage/framework/views` is not writable or restricted by aaPanel's `open_basedir`. In aaPanel Site Settings, disable "Anti-XSS attack (Base directory limit / open_basedir)" or append `/tmp` to allowed paths.
- **Nginx URL Rewrites**: Never rely on static file paths for Laravel routes. The Nginx server block must include:
  ```nginx
  location / {
      try_files $uri $uri/ /index.php?$query_string;
  }
  ```
- **Storage Symlink**: Always execute `php artisan storage:link` so photos and uploaded documents in `storage/app/public` are accessible via web URL.

## 2. KSP Cooperative Risk Management & Collectibility Standards
- **OJK 5-Stage Collectibility**:
  - Kol 1 (Lancar): 0 days overdue &rarr; Stage: `normal`
  - Kol 2 (DPK): 1–30 days overdue &rarr; Stage: `reminder`
  - Kol 3 (Kurang Lancar): 31–60 days overdue &rarr; Stage: `sp1`
  - Kol 4 (Diragukan): 61–90 days overdue &rarr; Stage: `sp2`
  - Kol 5 (Macet / NPL): > 90 days overdue &rarr; Stage: `sp3`
- **Maker-Checker & Segregation of Duties**:
  - The teller who receives an application cannot approve it.
  - The credit analyst who recommends a loan cannot approve it.
  - Restructuring requests (penalty waivers, rescheduling) must be decided exclusively by Managers or Administrators.
- **Atomic Penalty Restructuring**:
  - Waived penalties must be deducted from individual overdue schedules (`loan_schedules.penalty_amount`) and master balance (`loans.outstanding_penalty`) inside a database transaction (`DB::transaction`).

## 3. UMKM Business Photo Verification
- **Dual-Mode Photo Input**: Provide both live camera capture via HTML5 `navigator.mediaDevices.getUserMedia` (Base64 Canvas snapshot) and file upload fallback (`capture="environment"` for mobile browsers).
- **Two-Sided Evidence**: Pair the borrower's self-submitted business photo with the credit analyst's on-site field survey photo and verification notes before managerial approval.

