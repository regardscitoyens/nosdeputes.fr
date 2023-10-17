const fs = require('fs');
const https = require('https')

/*
AN format:
{
    acteurId: 'OMC_PA2150',
    civ: 'M.',
    nom: 'Mélenchon',
    prenom: 'Jean-Luc',
    group: 'La France insoumise',
    departement: 'Bouches-du-rhône',
    numDepartement: '13',
    numCirco: '4',
    communes: 'Marseille 1er arrondissement (13001),Marseille 5eme arrondissement (13005),Marseille,Marseille 2eme arrondissement (13002),Marseille 3eme arrondissement (13003),Marseille 6eme arrondissement (13006)'
  }

RC format:
  ??;nodep;nomcommune;nocirco;code
  001;01;L'Abergement-Clémenciat;04;01400
*/

const code_postaux_regexp = /\(([0-9]*?)\)/g
const pad = x => x.length > 1 ? x : '0' + x
function anToRc(obj) {
  const nodep = pad(obj.numDepartement)
  const nocirco = pad(obj.numCirco)
  const communes = obj.communes.split(',').map(commune => {
    const name = commune.replace(code_postaux_regexp, "").trim()
    const code_matches = commune.match(code_postaux_regexp)
    const code = code_matches && code_matches[0].slice(1, -1)
    return {code, name}
  })
  return communes.map(({code, name}) => `;${nodep};${name};${nocirco};${code}`)
}

async function main() {
  let request = new Promise((resolve, reject) => {
    const req = https.get("https://www.assemblee-nationale.fr/dyn/ajax/deputes/get-deputes-data", res => {
      let body = "";
      res.on("data", (chunk) => body += chunk);
      res.on("end", () => resolve(body));
    }).on('error', reject)
    req.end()
  })
  let response = await request
  let listdepute = JSON.parse(response).data;
  let rccsv = listdepute.flatMap(anToRc)
  for (line of rccsv) console.log(line)
}

main()
