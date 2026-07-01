import fetch from 'node-fetch';

export default async function handler(req, res) {
  const response = await fetch('https://bake-co.infinityfree.me/Back-End/products-api.php', {
    headers: {
      'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      'Accept': 'application/json, text/plain, */*',
      'Referer': 'https://bake-co.vercel.app',
    }
  });

  const text = await response.text();

  try {
    const data = JSON.parse(text);
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.status(200).json(data);
  } catch (e) {
    res.status(500).json({ error: 'Backend returned invalid JSON', raw: text.slice(0, 300) });
  }
}