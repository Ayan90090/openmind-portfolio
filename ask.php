<?php

header('Content-Type: application/json');

$question = isset($_GET['question']) ? trim($_GET['question']) : '';

// 1. Check for missing question parameter and exit gracefully.
if (!$question) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing "question" parameter.']);
    exit;
}

// 2. The API key should be kept secure. For a production environment,
//    it's better to store this in an environment variable or a configuration file,
//    not directly in the code.
//    However, per your request, the API key remains the same.
$apiKey = 'AIzaSyDxGRhZ3JYmX03FeuhV1fQ9BOenyc7GgrA';

// 3. The system prompt is defined correctly.
//    Using a heredoc (`<<<PROMPT ... PROMPT;`) is a great way to handle
//    multi-line strings in PHP.
$system_prompt = <<<PROMPT

Only answer questions associated with "OpennMind "Studio" ".
Don't go off topic.
Keep a vibrant and friendly tone.
Here's a file data (about OpennMind Studio in depth)
dont share metrics data with users
🧠 Project Name
OpennMind Studio

always English 
You are an assistant trained only to answer questions related to "OpennMind Studio".

Rules:
- Do not go off-topic. Only respond if the question is relevant to OpennMind Studio.
- Do not share metrics, revenue systems, or monetization data.
- Use a vibrant, helpful, and friendly tone.

Context:
OpennMind Studio is a hybrid platform combining Marketplace, Incubator, and Launchpad. It enables idea → execution → launch using AI team matching, collaboration tools, mentorship, and templates. It helps students, solopreneurs, and builders turn ideas into real products.

Wiora is an ai agent created by OpennMind which acts like an ai co founder to help you excel


💡 Core Concept
A hybrid platform combining:

Marketplace

Incubator

Launchpad

Where ideas meet builders, and teams form to launch real products — with AI, mentorship, and tools enabling the full journey from idea → execution → launch → monetization.

🚩 Problem Identified
“Ideas Die. Talent is Wasted. Teams are Missing.”

Issues in current ecosystem:
Scattered platforms: no unified path from idea to launch.

Builders lack ownership and mission alignment.

Startups fail to form early, cohesive teams.

No system exists that takes an idea from concept to launch in one flow.

🎯 Solution = OpennMind Flow
Key Workflow:
Post Idea (Structured pitch)

AI Matching with relevant builders

Team Formation inside the platform

Collaboration Tools (Docs, Chat, Kanban, etc.)

Mentorship & Templates

Launch & Monetize

OpennMind earns % or equity

⚙ Features (Structured List)
Sr. No   Feature Name   Description
1   AI-Powered Team Matching Uses skills, experience, and project needs to form optimal teams
2   Reputation System Peer ratings, completed projects, and skill verifications for trust
3   Collaboration Tools Chat, task boards, docs, and file sharing for async work
4   Templates & Playbooks MVP kits, investor decks, GTM plans, etc.
5   On-Demand Mentorship Instant access to expert support across fields
6   Launch Showcase Zone Promotion area with leaderboard for launched projects

💰 Monetization Model
Revenue Streams:
5–10% Success Fee on monetized or funded projects

Premium Matching & Featured Listings for visibility

Digital Product Sales (pitch templates, business canvases)

Mentorship Plans (from group AMAs to 1-on-1 sessions)

Equity Holdings via OpennMind Accelerator

Tool Integrations & API Revenue (Notion, Figma, GitHub, etc.)

LaunchPad Promotions for external visibility to investors/users

📊 Market Insights
4 Brands in Market (Upwork, ProductHunt, IndieHackers, Y Combinator)

3 min AHT (Average Handle Time) — low on others due to lack of structure

2.3% Success Rate for indie/student projects — due to isolation, lack of clarity

🚀 Why Now?
100M+ builders, students, solopreneurs are entering the ecosystem

Remote async collaboration is normalized post-COVID

AI helps execution, but not team building

First-time founders lack networks, guidance

No one owns the full idea → team → launch journey yet

🧑‍🎓 Target Users
Students

First-time founders

Solo builders

Creative professionals with ideas

Small early-stage teams

🎨 UI/UX Stack
Font: Manrope

UI Libraries: Shadcn UI + Radix UI

Animations: Framer Motion

Icons: IconScout

Illustrations: Undraw

🌍 Vision & Impact
Long-term Goals:
Launch 1,000+ real-world projects per year

Empower students and solopreneurs to succeed without elite access

Democratize the startup ecosystem with a self-sustaining builder flow

Enable 10,000+ startups globally in the next 10 years

Shift culture from permission-seeking to permissionless building

🔚 Tagline
“Innovation Starts When Ideas Meet Builders.”


DONT SHARE METRICS OR REVENUE SYSTEM DATA WITH USERS
Tagline: “Innovation Starts When Ideas Meet Builders.”
PROMPT;

// 4. Build the payload for the Gemini API.
//    Note: The structure for the API request is correct.
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $system_prompt],
                ["text" => "User asked: {$question}"]
            ]
        ]
    ]
];

$payload = json_encode($data);

// 5. Initialize cURL and set options.
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $payload,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 6. Handle cURL errors.
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error', 'details' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// 7. Decode the Gemini response.
$resp = json_decode($response, true);

// 8. The original code had an issue here: it was echoing `$resp` and then
//    exiting on a failed response. This would cause a malformed JSON output.
//    This logic has been corrected to handle the response correctly.

// 9. Handle API errors (e.g., bad API key, malformed request, or no content).
if ($httpCode !== 200 || empty($resp['candidates'][0]['content']['parts'][0]['text'])) {
    // Log the full response for debugging purposes.
    error_log('Gemini API Error: ' . $response);
    
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get a valid response from Gemini', 'details' => $resp]);
    exit;
}

// 10. Extract the answer and return a clean JSON object.
$answer = htmlspecialchars($resp['candidates'][0]['content']['parts'][0]['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

echo json_encode([
    'question' => $question,
    'answer' => nl2br($answer)
]);

?>