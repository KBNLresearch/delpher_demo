<?php
    define('STYLE', 'default');
    include "shared/header.php" ?>
    <div class = 'row information'>
        <div class = 'col-sm handleiding_text'>
            <h4 class = 'pink'>Achtergrondinformatie</h4>
            <p>
                Deze site is ontwikkeld om een idee te geven van de mogelijkheden met de data die beschikbaar is op Delpher.
                Voor wie ook graag zelf aan de slag gaat, is code beschikbaar gemaakt via Github.
            </p> 
            <p>
                Deze website is geoptimaliseerd voor gebruik met een gemaximaliseerde browser op een laptop of pc, voor de browsers Firefox, Chrome en Edge. 
                Als deze website gebruikt wordt op andere apparaten of browsers, kan er functionaliteit verloren gaan. 
            </p>
            <p>
                Hieronder vindt u meer informatie over Delpher en het gebruik van deze site. 
            </p>
            
            <b>Delpher</b>
            <p>
                <a href="https://www.delpher.nl" class = "handleiding" target='_blank'>Delpher</a> geeft toegang tot gedigitaliseerde Nederlandse historische teksten uit de collecties van wetenschappelijke instellingen, bibliotheken en erfgoedinstellingen. 
                Delpher maakt het mogelijk om miljoenen gedigitaliseerde pagina’s uit Nederlandse boeken, kranten en tijdschriften op één centrale plek op woordniveau te doorzoeken. 
            <p>
                Voor deze website is gebruik gemaakt van het krantenarchief van Delpher.nl 
            </p>
            <b>De tijdlijn</b>
            <p>
                De tijdlijn geeft per onderwerp de jaren weer waarin een krantenartikel over dit onderwerp is gevonden. 
                Bij de tijdlijn zijn een aantal visualisaties te vinden, zoals een wordcloud, een lijngrafiek, een staafdiagram en een wordtree bij subselecties.
                Voor meer informatie over deze visualities kunt u op het informatie-icoontje (de afbeelding van een 'i' in een rondje) klikken. 
            </p> 
                De diagrammen zijn gebaseerd op de gedigitaliseerde teksten zoals deze verkregen zijn van Delpher. 
                Deze teksten zijn geautomatiseerd gemaakt met OCR (Optical Character Recognition). Deze techniek levert geen 100% correct resultaat op.
                Dit komt mede doordat oude drukken moeilijker te lezen zijn met software dan moderne. Dat betekent dat er onjuiste tekens in de tekst, en dus bijvoorbeeld ook 
                in de wordcloud, kunnen voorkomen.
            </p>
            <b>De code</b>
            <p>
                Op de <a href="https://github.com/KBNLresearch/delpher_demo" class = "handleiding" target='_blank'>Githubpagina</a> behorende bij deze website , zijn twee verschillende soorten code beschikbaar gemaakt. 
            </p>
            <p>
                Ten eerste zijn daar Jupyter Notebooks te vinden, waarin Python code staat voor het voorbewerken en visualiseren van de data, zoals dat ook op deze website is gebeurd. 
		U kunt de csv files die u op deze website kunt downloaden, bewerken met deze code. Daarnaast is er ook een voorbeeld dataset beschikbaar op de Githubpagina. 
            </p>
            <p>
                Daarnaast is ook de broncode van deze website beschikbaar gemaakt. Deze code kan gebruikt worden om een soortgelijke website te bouwen, of om een verbeterde versie van
                deze website te maken. Schroom vooral niet om uw eindresultaat met ons te delen!
            </p>
            
            <b>De data</b>
            <p>
                Wil je zelf aan de slag met eigen onderwerpen? </br>
		Een deel van de krantenartikelen zijn nog auteursrechtelijk beschermd.
                Hergebruik van deze data is daarom alleen onder bepaalde voorwaarden mogelijk. 
                Voor meer informatie hierover en om toegang tot deze data uit het krantenarchief te verkrijgen, kun je contact opnemen met 
                de <a href="https://www.kb.nl/bronnen-zoekwijzers/dataservices-en-apis" class = "handleiding" target='_blank'>dataservice</a> van de Nationale Bibliotheek van Nederland via dataservices@kb.nl. 
            </p>
            <b>Contact</b>
            <p>
                Voor vragen of meer informatie over deze site kunt u contact opnemen met Mirjam Cuper, via mirjam.cuper@kb.nl 
                (gedurende de periode van 22 december 2020 tot en met 4 januari 2020 kan het enkele dagen duren voor u een reactie ontvangt vanwege de kerstperiode).
            </p>

        </div>
    <div class = 'col-sm handleiding_links'>
        <h4>Links</h4>
        <p>
        <a href="https://www.delpher.nl" class = "handleiding" target='_blank'>Delpher</a></br>
            Het Nederlandse historisch krantenarchief. 
        </p>    
        <p>
        <a href="https://github.com/KBNLresearch/delpher_demo" class = "handleiding" target='_blank'>Jupyter Notebook</a></br>
            De Githubpagina met de Jupyter Notebooks en de broncode van deze website. 
        </p>
        <p>
        <a href="https://www.kb.nl/bronnen-zoekwijzers/dataservices-en-apis" class = "handleiding" target='_blank'>Dataservices KB</a></br>
            Hier vindt u meer informatie over de dienst Dataservices van de KB, en de verschillende datasets die beschikbaar zijn. 
        </p>
        <p>
        <a href="https://lab.kb.nl/" class = "handleiding " target='_blank'>KB Lab</a></br>
            Deze website is een demo, gemaakt door de afdeling onderzoek van de Nationale Bibliotheek van Nederland (KB) als onderdeel van het KB lab. 
            Op het KB lab vindt u alle experimentele demo's en datasets gebaseerd op de digitale collecties van de KB. 
        </p>
    </div>
</div>
</div>
</div>
<?php include "shared/handleiding_footer.php" ?>


