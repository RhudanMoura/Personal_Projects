
      $(document).ready(function () {
        $('.faq-question').click(function () {
          $('.faq-answer').not($(this).next()).slideUp();
          $(this).next().slideToggle();
        });
      });