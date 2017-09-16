<script type="text/javascript" src="http://cache.download.banner.greenjade88.com/integrationjs.php"></script>
<script type="text/javascript">
    iapiSetCallout('Login', calloutLogin);
    <?php $ptPlayerAccount = new \App\Vendor\GameGateway\PT\PTGameAccount($playerLoginPageEntity->gameAccount) ?>
    iapiLogin('<?= $ptPlayerAccount->loginUserName() ?>', '<?=  $ptPlayerAccount->loginPassword() ?>', 1, "ch");
    var requestId = iapiRequestIds[0][0];
    function calloutLogin(response) {
        if(response.errorCode == 0){
            window.location.href = 'http://cache.download.banner.greenjade88.com/casinoclient.html?language=ZH-CN&game=<?=$playerLoginPageEntity->gameCode?>'
        }
    }
</script>