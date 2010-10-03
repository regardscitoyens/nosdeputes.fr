  <div class="searchbox">
  <form action="">
  <p>
     <input name="search" id="search" value="<?php echo str_replace('"', '&quot;', strip_tags($sf_request->getParameter('query'))); ?>" />
    <input type="submit" value="Rechercher"/>
  </p>
  </form>
  </div>
