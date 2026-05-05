</div> </main>

</div> <script>
    const toggleBtn = document.getElementById('toggleBtn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
        if (sidebar.style.marginLeft === '-260px') {
            sidebar.style.marginLeft = '0';
        } else {
            sidebar.style.marginLeft = '-260px';
        }
    });
</script>
</body>
</html>