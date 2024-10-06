<content class="content">
   <section>
      <!-- FAQ Keyword Search Start -->
      <?php include 'filter.php'; ?>
      <!-- FAQ Keyword Search End -->
      <div class="container faq-container">
         <h1><?php echo ucfirst($faq[0]['question']); ?></h1>
         <br>
         <h4><?php echo ucfirst($faq[0]['answer']); ?></h4>
         <br>
      </div>
      <!-- Was Info Helpful Start -->
      <?php include "footer.php"; ?>
      <!-- Was Info Helpful End -->
   </section>
</content>