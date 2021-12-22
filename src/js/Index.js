jQuery(function ($) {
    $(document).ready(function () {

        
        var checkModuleInit = setInterval(function () {
            if (!Modularity.Editor.Module.initCompleted) return;
            clearInterval(checkModuleInit);
            setModuleType()
        }, 200);


    });

  

    function setModuleType() {
        $(moduleData).each(function (index, { id, usage }) {
            var item = $(`li#post-${id}`).get(0);
            if (item) {
                var itemTitle = $(item).find(".modularity-module-title").get(0);
                var text = usage.length > 1 ? "Global" : "Låst";

                $("<span class='modularity-module-type'> – " + text + "</span>").insertAfter( $(itemTitle));
            };
        });
    };

}(jQuery));

