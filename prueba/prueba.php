
<form>
<td width="94">Negotiated price:*</td>
<td width="165"><input type= "text" name="nprice" /></td>
</tr>
<tr>
<td>Sales tax:*</td>
<td><input type="text" name="tax"/></td>
<td width="53">%</td>
</tr>
<tr>
<td height="31">Term:</td>
<td><select name="term">
<option value="10">10 months</option>
<option value="48">48 months</option>
<option value="60">60 months</option>
</select> </td>
</tr>
<tr>
<td>Interest Rate:*</td>
<td><label>
<input type="irate" name="rate"/>
</label> </td>
<td>%</td>
</tr>
<tr>
<td>Down payment:</td>
<td><label>
<input type="downpayment" name="payment" />
</label> </td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input name="Input" type="button" value="Calculate payments" onclick="calcpayments()"/></td>
<td><input name="reset2" type="reset" value="Clear" /></td>
</tr>
</table>
<br />
<b>Your estimated monthly payment is:</b>
<div id="monthlypayment"></div>
</form>

<script>

function calcpayments()

{

//Las cinco variables utilizadas en la fórmula

var nprice=document.forms[0].nprice.value*1;
var salestax=document.forms[0].tax.value*1;
var interest=document.forms[0].rate.value*1;
var dpayment=document.forms[0].payment.value*1;

var t;

//Usamos un bucle para seleccionar el plazo de pagos

for (i=0; i<document.forms[0].term.options.length; i++)

{

if (document.forms[0].term.options[i].selected)
t = document.forms[0].term.options[i].value*1;
}

//Esta es la fórmula que realiza el cálculo

var result=(nprice*(salestax/100 +1)-dpayment)*((interest/100)/12) / (1-Math.pow((1+(interest/100)/12),(-t)));

result=Math.round(result*100) /100;


//Esta última línea muestra el resulado dentro del casillero

document.getElementById("monthlypayment").innerHTML=result;

}
