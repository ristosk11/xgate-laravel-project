import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/login');
  await page.screenshot({ path: 'login-screenshot.png' });
  console.log("Screenshot saved to login-screenshot.png");
  
  // Check if tailwind classes are applied by looking at computed styles
  const btn = await page.$('button');
  if (btn) {
    const bgColor = await page.evaluate(el => window.getComputedStyle(el).backgroundColor, btn);
    console.log("Button background color:", bgColor);
  }
  
  await browser.close();
})();
