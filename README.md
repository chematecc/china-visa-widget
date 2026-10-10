# China Visa Check widget

A free checker you can put on any website. Your visitors pick their passport and see whether they need a visa for mainland China: 30-day visa-free entry, 240-hour transit, Hainan, or a visa.

- 194 passports, 10 languages, light and dark colours
- Served from [chinavisacheck.com](https://chinavisacheck.com/china-visa/), so the answers follow the rules when China changes them. The rules are checked against the official Chinese sources every day.
- No sign-up, no ads, no cookies, no personal data

**Live preview and settings:** https://chinavisacheck.com/china-visa/embed-widget

## Embed

Paste this where your site takes HTML:

```html
<iframe src="https://chinavisacheck.com/china-visa/embed" title="China Visa Check" width="100%" height="320" style="border:0;max-width:520px" loading="lazy"></iframe>
<script>addEventListener("message",function(e){if(e.origin==="https://chinavisacheck.com"&&e.data&&e.data.cvcH)document.querySelectorAll('iframe[src^="https://chinavisacheck.com/"]').forEach(function(f){if(f.contentWindow===e.source)f.style.height=e.data.cvcH+"px"})})</script>
<p><a href="https://chinavisacheck.com/china-visa/">China Visa Check</a></p>
```

The `<iframe>` is the widget. The `<script>` line lets it set its own height, so nothing is cut off and there is no scroll bar; where a platform removes scripts, the widget still works at the fixed height. Please keep the link under it.

## Options

Everything is set in the address of the iframe.

| Option | How | Example |
|---|---|---|
| Language | Put the language before `/china-visa/embed` | `https://chinavisacheck.com/ja/china-visa/embed` |
| Starting passport | Add `#` and the two-letter country code | `…/china-visa/embed#jp` |
| Colours | Add `#light` or `#dark`. Without it the widget follows the visitor's device. | `…/china-visa/embed#dark` |
| Passport and colours | Join them with `-` | `…/china-visa/embed#jp-dark` |

Languages: English (no prefix), `zh` 简体中文, `tc` 繁體中文, `ko` 한국어, `ja` 日本語, `ru` Русский, `es` Español, `de` Deutsch, `fr` Français, `ar` العربية.

Without a starting passport the widget guesses from the visitor's browser language and they can change it.

## Where to paste it

| Platform | Steps |
|---|---|
| WordPress | Add a **Custom HTML** block and paste |
| Wix | Add Elements → Embed Code → **Embed HTML** → paste |
| Squarespace | Add a **Code** block → paste |
| Webflow | Add an **Embed** element → paste |
| Shopify | Online Store → Themes → Customize → add a **Custom Liquid** section → paste |
| Blogger | Layout → Add a Gadget → **HTML/JavaScript** → paste |
| Google Sites | Insert → Embed → **Embed code** → paste |
| Notion | Type `/embed` and paste the address `https://chinavisacheck.com/china-visa/embed` |
| React, Next.js | [`examples/react.jsx`](examples/react.jsx) |
| Plain HTML | [`examples/basic.html`](examples/basic.html) |

## Privacy

The widget sets no cookies and collects no personal data. It tells chinavisacheck.com which website it is shown on (the host name only), so we can see where it is used.

## Terms

Free for any website, commercial or not. Please keep the "China Visa Check" link, and don't present the widget as an official or government service: China Visa Check is an independent guide. Rules can change without notice; the widget shows the date of the last check.

Need a different size, or your own version? Write to partners@chinavisacheck.com.

## Data

The rules behind the widget are open data (CSV and JSON, CC BY 4.0): [chematecc/china-visa-rules](https://github.com/chematecc/china-visa-rules).

## Licence

The code in this repository is under the [MIT licence](LICENSE). The answers shown by the widget come from chinavisacheck.com (CC BY 4.0, credit "China Visa Check (chinavisacheck.com)").
