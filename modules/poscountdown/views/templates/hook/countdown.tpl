{if $enddate!=null && $enddate >0 }

    <script type="text/javascript">
        $(document).ready(function () {
            $('#future_date_{$id_cate}_{$idproduct}').countdown({
                until: new Date({$enddate|date_format:"%Y"}, {$enddate|date_format:"%m"}-1, {$enddate|date_format:"%d"}, {$enddate|date_format:"%H"}, {$enddate|date_format:"%M"}, {$enddate|date_format:"%S"}),
				labels: ['{l s='Years' mod='poscountdown'}', '{l s='Months' mod='poscountdown'}', '{l s='Weeks' mod='poscountdown'}', '{l s='Days' mod='poscountdown'}', '{l s='Hrs' mod='poscountdown'}', '{l s='Mins' mod='poscountdown'}', '{l s='Secs' mod='poscountdown'}'],
				labels1: ['{l s='Year' mod='poscountdown'}', '{l s='Month' mod='poscountdown'}', '{l s='Week' mod='poscountdown'}', '{l s='Day' mod='poscountdown'}', '{l s='Hour' mod='poscountdown'}', '{l s='Minute' mod='poscountdown'}', '{l s='Second' mod='poscountdown'}'],

			});
        });
    </script>
{/if}
