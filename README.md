# API Key Configuration

Before running the project, you must update your API keys in two files: **ask.php** and **join.php**.

---

Image
<img width="1902" height="910" alt="image" src="https://github.com/user-attachments/assets/b65badf6-49e1-40e4-b251-47327fd34453" />

Demo: 
https://srv1070916.hstgr.cloud/portfolio/openmind-portfolio-main/

## 1️⃣ Updating Gemini API Key (ask.php)

- Open the `ask.php` file in your editor.
- Locate the line where the Gemini API key is set.
- Replace the placeholder text with your actual Gemini API key from Google AI Studio.
- Save the file.

---

## 2️⃣ Updating Baserow API Key and Table Details (join.php)

- Open the `join.php` file in your editor.
- Find the section where the Baserow API token and table ID are defined.
- Replace the placeholder API token with your actual Baserow API key.
- Replace the placeholder table ID with your actual table ID.
- Update the field IDs to match the structure of your Baserow table.
- Save the file.

---

## ⚠️ Security Note

Do not upload your real API keys to a public repository.  
Always use `.env` files or environment variables to store sensitive information when deploying the project.
