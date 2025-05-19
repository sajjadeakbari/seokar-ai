=== SeoKar AI ===
Contributors: sajjadeakbari
Donate link: https://sajjadakbari.ir/donate
Tags: ai, seo, content, gpt, openai, google ai, gemini, hugging face, artificial intelligence, content generation, keyword suggestion, title suggestion, content optimization, wordpress seo
Requires at least: 5.5
Tested up to: 6.5
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.4

SeoKar AI empowers your WordPress content creation and optimization workflow using the power of cutting-edge Artificial Intelligence models from OpenAI, Google, and Hugging Face.

== Description ==

Supercharge your WordPress SEO and content strategy with SeoKar AI! This plugin seamlessly integrates leading AI services directly into your WordPress editor and admin panel.

**Key Features:**

*   **Multi-API Support:** Connect your API keys for:
    *   OpenAI (GPT models like GPT-3.5-turbo, GPT-4)
    *   Google AI (Gemini models)
    *   Hugging Face (various public models via Inference API)
*   **In-Editor Assistance (Classic & Gutenberg):** A convenient meta box provides:
    *   **Title Suggestions:** Generate multiple SEO-friendly and engaging titles based on your content.
    *   **Keyword Ideas:** Discover relevant keywords to target.
    *   **Content Outline Generation:** Quickly create a structured outline for your posts or pages.
    *   **Full Content Generation (with caution):** Draft initial content based on a title or outline.
    *   **Category Suggestions:** Get AI-powered recommendations for appropriate categories.
    *   **Tag Suggestions:** Find relevant tags to improve content discoverability.
*   **Published Content Analysis (Admin/Editor View):** A discreet icon on published posts/pages (visible only to admins/editors) allows you to:
    *   Get quick AI-powered SEO analysis and improvement suggestions for that specific page.
*   **User-Friendly Settings:** Easily configure your API keys and select preferred models.
*   **Secure & Standardized:** Built with WordPress coding standards, security best practices (nonces, sanitization, escaping), and ready for translation.

Whether you're a blogger, marketer, or website owner, SeoKar AI helps you save time, overcome writer's block, and enhance the quality and SEO-friendliness of your WordPress content.

**How it works:**
You provide your API keys for the AI services you wish to use. The plugin then sends requests (prompts) based on your current content or inputs to these services and displays the AI-generated suggestions directly within your WordPress interface.

**Disclaimer:**
While SeoKar AI strives to provide helpful suggestions, AI-generated content should always be reviewed, edited, and fact-checked by a human before publishing. The quality of suggestions depends on the AI model used and the quality of the prompt. Usage of AI services may incur costs based on your API provider's pricing.

== Installation ==

1.  **Download:** Download the `seokar-ai.zip` file from the WordPress Plugin Directory (once approved) or from the GitHub repository.
2.  **Upload via WordPress Admin:**
    *   Navigate to `Plugins > Add New` in your WordPress admin panel.
    *   Click `Upload Plugin`.
    *   Choose the `seokar-ai.zip` file and click `Install Now`.
3.  **Alternatively, Upload via FTP:**
    *   Unzip the `seokar-ai.zip` file.
    *   Upload the extracted `seokar-ai` folder to the `/wp-content/plugins/` directory on your server.
4.  **Activate:** Activate the plugin through the 'Plugins' menu in WordPress.
5.  **Configure:** Go to the "SeoKar AI" menu in your WordPress admin panel. Enter your API keys for the services you want to use and save the settings. You may also want to select preferred AI models if available.

== Frequently Asked Questions ==

= Which AI services are supported? =
Currently, SeoKar AI supports OpenAI (GPT models), Google AI (Gemini models), and Hugging Face (via their Inference API for public models). You need to provide your own API keys.

= How do I get API keys? =
*   **OpenAI:** Visit [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)
*   **Google AI (Gemini):** Visit [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey) (for Gemini API)
*   **Hugging Face:** Visit [https://huggingface.co/settings/tokens](https://huggingface.co/settings/tokens) (create an Access Token with `read` role, or `write` if using models that require it, though usually `read` is for inference).

= Is the plugin free? =
The SeoKar AI plugin itself is free and open-source. However, using the AI services (OpenAI, Google AI, Hugging Face) through their APIs typically involves costs based on your usage. Please check the pricing pages of each respective AI provider.

= How are my API keys stored? =
API keys are stored in your WordPress database using standard WordPress options functions. We recommend securing your WordPress installation properly.

= Can I choose which AI model to use? =
Yes, the settings page will allow you to select preferred models for services that offer multiple options (e.g., different GPT versions, or specific Hugging Face models if you know their identifiers).

= Does it work with the Gutenberg editor? =
Yes, the suggestions meta box will appear in both the Classic Editor and the Gutenberg (Block) Editor. The icon for published content analysis also works independently of the editor used.

= Where can I get support or report a bug? =
Please visit the plugin's support forum on WordPress.org or open an issue on our GitHub repository: [https://github.com/sajjadeakbari/aiseokar/issues](https://github.com/sajjadeakbari/aiseokar/issues)

== Screenshots ==
1.  The SeoKar AI settings page for API key configuration and model selection.
2.  The SeoKar AI suggestions meta box in the post editor.
3.  Example of AI-generated title suggestions.
4.  The AI suggestion icon on a published page (admin/editor view).
5.  The modal displaying AI analysis for a published page.

== Changelog ==

= 0.2.0 =
*   Refactored API Handler for better extensibility and error handling.
*   Improved UI for settings page with model selection and API test buttons.
*   Enhanced metabox UI with clearer loading states and copy-to-clipboard functionality.
*   More detailed and context-aware prompts for AI.
*   Added `uninstall.php` for cleaner deactivation.
*   Improved security with stricter sanitization and escaping.
*   Full i18n review and .pot file update.
*   Enhanced admin notices for API key status.
*   Code refactoring for better adherence to WordPress Coding Standards.

= 0.1.0 =
*   Initial release. Basic functionality for API key input, metabox in editor, and AI suggestion icon on frontend.

== Upgrade Notice ==

= 0.2.0 =
This version includes significant improvements to the API handling, user interface, and security. Please review your API settings after upgrading.
