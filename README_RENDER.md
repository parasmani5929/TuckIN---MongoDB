Render deployment steps (concise)

1. Create a Git repo for this project and push it to GitHub.

2. On Render.com, create a new **Web Service** and connect your GitHub repo.

3. Settings:
   - Environment: `PHP`
   - Build Command: `composer install --no-dev --prefer-dist --no-interaction --no-progress`
   - Start Command: `php -S 0.0.0.0:$PORT -t .`

4. In Render's Environment settings, add these environment variables:
   - `MONGODB_URI` — your full Atlas connection string (use URL-encoded password)
   - `MONGODB_DB` — `food_ordering` (or your DB name)

5. MongoDB Atlas network access: add Render's outbound IPs or (for quick setup) allow access from `0.0.0.0/0` temporarily. For production, use VPC peering or restrict to specific ranges.

6. Deploy and verify the service URL. If database connection fails, check logs (Render dashboard) — secrets won't be printed.

Notes:
- Do NOT commit `.env` with real credentials. Use the `.env.example` as a template.
- If you need, I can create the Git repo and help push the code.
