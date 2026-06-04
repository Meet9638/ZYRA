import asyncio
from playwright.async_api import async_playwright
from playwright_stealth import stealth_async

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(
            headless=False,  # Set to True for production
            args=[
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        )
        context = await browser.new_context(
            user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            viewport={'width': 1280, 'height': 720}
        )
        page = await context.new_page()

        # Apply stealth to avoid detection and captcha
        await stealth_async(page)

        # Navigate to Immobiliare.it
        await page.goto("https://www.immobiliare.it/")

        # Wait for the page to load
        await page.wait_for_load_state('networkidle')

        # Example: Search for apartments in Milan
        # Fill search form
        await page.fill('input[name="q"]', 'Milano')
        await page.click('button[type="submit"]')

        # Wait for results
        await page.wait_for_selector('.listing-item')

        # Extract data
        listings = await page.query_selector_all('.listing-item')
        for listing in listings[:5]:  # Limit to first 5 for example
            title = await listing.query_selector('.titolo')
            price = await listing.query_selector('.prezzo')
            if title and price:
                title_text = await title.inner_text()
                price_text = await price.inner_text()
                print(f"Title: {title_text}, Price: {price_text}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
