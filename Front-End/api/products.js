export default async function handler(req, res) {
  const response = await fetch('https://bake-co.infinityfree.me/Back-End/products-api.php');
  const data = await response.json();

  res.setHeader('Access-Control-Allow-Origin', '*');
  res.status(200).json(data);
}