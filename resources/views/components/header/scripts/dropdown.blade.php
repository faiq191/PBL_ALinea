<script>
    // 1. Fungsi Toggle Dropdown
    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById("profileMenu").classList.toggle("hidden");
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
    }

    function toggleNotifDropdown(event) {
        event.stopPropagation();
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.toggle("hidden");
        document.getElementById("profileMenu").classList.add("hidden");
    }

    window.addEventListener("click", function() {
        const menu = document.getElementById("profileMenu");
        if (menu) menu.classList.add("hidden");
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
    });
</script>
