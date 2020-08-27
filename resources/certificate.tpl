<style type="text/css">{literal}
  #page {
    position: absolute;
    top: 0;
    left: 0;
    width: 210mm;
    height: 296.5mm; /* 297mm causes a page break at the end. */
    background-image: url("{/literal}{crmResURL ext='civicrm.files'}/baumspenden/{contribution.baumspende_plant_region_key}{literal}.jpg");
    background-repeat: no-repeat;
    background-size: contain;
  }
  #page p {
    color: darkgreen;
    font-size: 24pt;
    font-family: sans-serif;
    position: absolute;
    text-align: center;
    width: 170mm;
    line-height: 10mm;
    vertical-align: middle;
  }

  #page p#certificate-name {
    top: 185mm;
    left: 20mm;
  }
  #page p#plant-info {
    top: 205mm;
    left: 20mm;
  }

  #contribution-id {
    position: absolute;
    top: 270mm;
    right: 0;
    -webkit-transform: rotate(-90deg);
    transform: rotate(-90deg);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
  }
  {/literal}</style>

<div id="page">
  <p id="certificate-name">{contribution.baumspende_certificate_name}</p>

  <p id="plant-info">{contribution.baumspende_amount} {contribution.baumspende_plant_tree}<br />
    {contribution.baumspende_plant_region} {contribution.baumspende_plant_period}
  </p>

  <div id="contribution-id">{contribution.id}</div>
</div>
