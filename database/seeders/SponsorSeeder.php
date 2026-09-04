<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            [
                'nome' => 'Crédito Agrícola',
                'link' => 'https://www.creditoagricola.pt',
                'ordem' => 1,
            ],
            [
                'nome' => 'Grupo Korisana',
                'link' => 'https://grupokorisana.com',
                'ordem' => 2,
            ],
            [
                'nome' => 'Farmácia Brandão',
                'link' => 'https://www.facebook.com/p/Farm%C3%A1cia-Brand%C3%A3o-100063788700580',
                'ordem' => 3,
            ],
            [
                'nome' => 'Retina Ótica Médica Lda',
                'link' => 'https://retinaopticamedica.pt',
                'ordem' => 4,
            ],
            [
                'nome' => 'AVIVALOR - SOCIEDADE AVÍCOLA, LDA',
                'link' => 'https://www.avivalor.pt',
                'ordem' => 11,
            ],
            [
                'nome' => 'FARMÁCIA MIRANDA',
                'link' => 'https://www.facebook.com/farmaciamirandamealhada',
                'ordem' => 12,
            ],
            [
                'nome' => 'ALVES BANDEIRA, LDA',
                'link' => 'https://www.alvesbandeira.pt/pt',
                'ordem' => 13,
            ],
            [
                'nome' => 'AUTO JVR',
                'link' => 'https://www.facebook.com/p/AUTO-JVR-Pintura-e-repara%C3%A7%C3%A3o-autom%C3%B3vel-100032786874298',
                'ordem' => 14,
            ],
            [
                'nome' => 'VALMAT - ARESTA HONORÁRIA Lda',
                'link' => 'https://valmatmateriais.pt',
                'ordem' => 15,
            ],
            [
                'nome' => 'CENTROPEÇAS - PEÇAS E ACESSORIOS DE AUTOMOVEIS LDA',
                'link' => 'https://www.facebook.com/centropecas.mealhada',
                'ordem' => 16,
            ],
            [
                'nome' => 'METALÚRGICA DO SUME LDA',
                'link' => 'https://www.sabemais.pt/informacao2/2/25687/101',
                'ordem' => 17,
            ],
            [
                'nome' => 'AUTO IC2 - IMPORTAÇÃO DE VEÍCULOS E PEÇAS AUTO LDA',
                'link' => 'https://autoic2.com/',
                'ordem' => 18,
            ],
            [
                'nome' => 'DORIAL SAÚDE MENTAL LDA',
                'link' => 'https://www.dorial.pt',
                'ordem' => 19,
            ],
            [
                'nome' => 'GRUEST PORTUGAL, LDA',
                'link' => 'https://gruest.com',
                'ordem' => 20,
            ],
            [
                'nome' => 'JOSÉ MANUEL PIMENTA & FILHO UNIPESSOAL LDA',
                'link' => 'https://www.facebook.com/jmpfilholda',
                'ordem' => 21,
            ],
            [
                'nome' => 'HOTEL EDEN',
                'link' => 'https://www.hoteleden.pt',
                'ordem' => 22,
            ],
            [
                'nome' => 'TALHO NOVO - PAMPILHOSA',
                'link' => 'https://www.facebook.com/ButcherShop.PortugueseButcher',
                'ordem' => 23,
            ],
            [
                'nome' => 'DEBASTOS LDA',
                'link' => 'https://www.debastos.pt',
                'ordem' => 24,
            ],
            [
                'nome' => 'SPOT OF BRANDS',
                'link' => 'https://www.spotofbrands.com/',
                'ordem' => 25,
            ],
            [
                'nome' => 'SOMENGIL, SA',
                'link' => 'https://somengil.com/pt',
                'ordem' => 101,
            ],
            [
                'nome' => 'CARLOS ROSA',
                'link' => 'https://www.autorosa.pt',
                'ordem' => 102,
            ],
            [
                'nome' => 'LOCOMOTIVA BA61',
                'link' => 'https://www.facebook.com/locomotivaba61',
                'ordem' => 103,
            ],
            [
                'nome' => 'INTERMARCHÉ MEALHADA',
                'link' => 'https://www.facebook.com/inter.mealhada/',
                'ordem' => 104,
            ],
            [
                'nome' => 'CITYSEG, LDA',
                'link' => 'https://www.cityseg.pt',
                'ordem' => 105,
            ],
            [
                'nome' => 'O SONHO DAS FONTES',
                'link' => 'https://www.sonhodasfontes.pt',
                'ordem' => 106,
            ],
            [
                'nome' => 'NÉ CHEF LDA',
                'link' => 'https://www.nechef.com',
                'ordem' => 107,
            ],
            [
                'nome' => 'VIA ROMANA',
                'link' => 'https://menu.via-romana.com',
                'ordem' => 108,
            ],
            [
                'nome' => 'J.M.D. NEVES LDA',
                'link' => 'https://www.facebook.com/p/JMD-NEVES-Unipessoal-LDA-100062922294742',
                'ordem' => 109,
            ],
            [
                'nome' => 'RESTAURANTE MAURÍCIO',
                'link' => 'https://pt.restaurantguru.com/Restaurante-Mauricio-Aguim',
                'ordem' => 110,
            ],
            [
                'nome' => 'ROCHAUTO',
                'link' => 'https://www.facebook.com/p/A-Oficina-Rochauto-100063648696991',
                'ordem' => 111,
            ],
            [
                'nome' => 'CURIA GRILL BAR',
                'link' => 'https://www.facebook.com/CuriaGrillBarRestaurante',
                'ordem' => 112,
            ],
            [
                'nome' => 'CURIVET LDA',
                'link' => 'https://curivet.pt',
                'ordem' => 113,
            ],
            [
                'nome' => 'FARMÁCIA TERMAL DA CURIA',
                'link' => 'https://farmaciatermal.com.pt',
                'ordem' => 114,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::updateOrCreate(
                ['nome' => $sponsor['nome']],
                [
                    'link' => $sponsor['link'],
                    'tipo' => Sponsor::TIPO_GERAL,
                    'ordem' => $sponsor['ordem'],
                    'ativo' => true,
                ],
            );
        }
    }
}
