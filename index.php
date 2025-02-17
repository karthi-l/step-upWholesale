<?php
include('includes/session_dbConn.php');
include('includes/bootstrap-css-js.php');
$imageDirectory = "brand_img/";

$main_query = " SELECT * FROM brands WHERE sub_brand IS NULL ";
$main_brand = $conn->query($main_query);
$sub_query = "SELECT * FROM brands WHERE sub_brand IS NOT NULL";
$sub_brand = $conn->query($sub_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .carousel-item{
            border-radius: 0.33rem;
        }
        .carousel-item img{
            width: 100%;
            height: 100%;
            object-fit:contain;
            border-radius: 0.33rem;
        }
    </style>
</head>
<body>  
    <!-- Including the main navbar -->
    <?php include('includes/main_nav.php'); ?>    
    <div class="container">

        <section class="hero row bg-light m-auto border  rounded  m-auto " id="hero-sec">
            <!-- Content Column -->
            <div class="hero-image col-lg-4 col-12 d-flex justify-content-center py-2">
            <img src="img/Walkaroo_logo.jpg" alt="Wholesale Footwear" class="border rounded-circle" width="192px">
        </div>
        <div class="hero-content col-lg-8 col-12 d-flex flex-column align-items-center text-center pt-3">
            <h1>Welcome to Saleem Traders</h1>
            <h2>It is a Step Up in Wholesale</h2>
            <p>Your trusted partner in wholesale footwear distribution.</p>
        </div>
    </section>
    <h2 class="display-6 display-xl-3">Brands we have dealership : </h2>
    <div id="carouselExampleControls1" class="carousel slide border rounded m-auto w-xl-75 h-xl-75 mt-1 " data-bs-ride="carousel">
        <?php if ($main_brand->num_rows > 0): ?>
            <div class="carousel-inner">
                <?php $isFirstItem = true; // Flag to mark the first item as active ?>
                <?php while($row = $main_brand->fetch_assoc()): ?>
                    <div class="carousel-item <?php echo $isFirstItem ? 'active' : ''; ?>">
                        <img src="<?php echo $imageDirectory . htmlspecialchars($row['image_file']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']); ?>" class="d-block w-100">
                    </div>
                    <?php $isFirstItem = false; // Set flag to false after the first item ?>
                <?php endwhile; ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls1" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls1" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        <?php endif; ?>
    </div>
    <h2 class="display-6 display-xl-3">Sub-brands we have dealership : </h2>
    <div id="carouselExampleControls2" class="carousel slide border rounded m-auto w-xl-75 h-xl-75 mt-1 " data-bs-ride="carousel">
        <?php if ($sub_brand->num_rows > 0): ?>
            <div class="carousel-inner">
                <?php $isFirstItem = true; // Flag to mark the first item as active ?>
                <?php while($row = $sub_brand->fetch_assoc()): ?>
                    <div class="carousel-item <?php echo $isFirstItem ? 'active' : ''; ?>">
                        <img src="<?php echo $imageDirectory . htmlspecialchars($row['image_file']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']).htmlspecialchars($row['sub_brand']); ?>" class="d-block w-100">
                    </div>
                    <?php $isFirstItem = false; // Set flag to false after the first item ?>
                <?php endwhile; ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls2" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls2" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        <?php endif; ?>
    </div>         
</div>

Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur qui nulla saepe architecto nesciunt, reprehenderit placeat quam, quisquam perferendis, dolorum libero quibusdam sed distinctio fuga. Nemo illum reiciendis laudantium quasi.
Sunt ratione repellat eligendi illum cumque laborum quidem accusantium nulla doloremque! Sapiente exercitationem distinctio illo dolorem molestias hic repellendus, numquam ratione ea cum quas in libero consequatur. Recusandae, tempore consequuntur.
Id architecto blanditiis, minus in inventore magnam, eaque, pariatur ipsum minima nihil possimus quae? Ipsa cupiditate iste aliquam, molestiae hic, dolorem debitis excepturi temporibus molestias, repellendus quasi mollitia doloribus in.
Alias, recusandae necessitatibus repudiandae illo consequuntur quisquam exercitationem soluta voluptas, vitae non amet obcaecati sed magnam aperiam? Rerum expedita hic, dolorem fuga, dolor perspiciatis earum amet non totam, rem repudiandae!
Necessitatibus voluptas nam cum fugiat, recusandae nihil ipsum corrupti laboriosam eius maxime voluptatum quae blanditiis non aliquid voluptates adipisci, praesentium velit. Fugiat perferendis vero laborum maxime fugit, explicabo magnam a!
Ad aspernatur ipsum minima! Enim dignissimos ipsum dolore, omnis quisquam dolorum fugiat ab at adipisci reiciendis maiores alias magnam laborum sed voluptas ea nemo delectus impedit earum tempora veniam minus?
Vel explicabo magnam nesciunt suscipit odit repellendus eaque debitis, dolorem possimus molestiae nulla tempora voluptates dolore totam, enim dolorum! Rem ullam voluptate maxime facere autem nobis aut corrupti sit iusto.
Pariatur ut recusandae quis id culpa alias. Facilis sint deleniti doloremque. Dolorem, voluptas quibusdam ipsam quod optio totam ullam quisquam adipisci. Delectus nostrum illum placeat quos, impedit hic. Itaque, rem.
Suscipit est dolorem dolor provident? Atque porro quisquam omnis nisi, eligendi iusto ratione tempore quos maiores unde assumenda alias sapiente aliquam aliquid cum rem nostrum iure non officiis nam hic.
Saepe modi praesentium corporis earum amet sed assumenda fugit sunt pariatur! Quibusdam harum odit voluptas quis officia libero natus, laboriosam nisi saepe cum explicabo non consequatur, tempore recusandae aspernatur veritatis!
Tenetur reiciendis similique earum natus dolorum hic ex rem id, praesentium voluptatem suscipit dolore consectetur commodi quis numquam recusandae fugit laudantium ea? Quas eligendi unde, ea eveniet molestiae libero error.
Ea assumenda corrupti soluta harum nam nihil architecto rem veniam aut mollitia repellendus distinctio aliquid minus sapiente tempore iusto excepturi, voluptates, quos impedit quaerat. Debitis possimus ducimus repellat dolor earum.
Earum pariatur expedita similique possimus modi excepturi fugit delectus ducimus rerum, aliquid totam fugiat vero facere quae eum exercitationem reiciendis quo tempora placeat quis alias. Praesentium necessitatibus non odit iusto!
Inventore repellat impedit quo consequuntur cum illo quia a veritatis ducimus quisquam animi nostrum, molestiae cumque eius adipisci minus blanditiis fuga! Magnam hic ratione sunt harum fugit dolore, sit inventore!
Obcaecati dolore assumenda iusto sunt quos, facilis necessitatibus adipisci perspiciatis animi repudiandae! Sed cumque quo recusandae numquam, voluptatem minima voluptas at ducimus eligendi iste expedita, molestiae culpa sunt quidem architecto.
Deserunt dicta suscipit quibusdam voluptatem porro, dolores et in nobis odit ex, praesentium officia excepturi consectetur ab recusandae voluptate. Dolorum eum provident aliquid laboriosam sint dignissimos, soluta debitis consectetur sit.
Dicta magni rem incidunt saepe facilis, dolore expedita? Accusamus nesciunt dolorum cum eaque ipsam ab iure distinctio. Ullam repudiandae a, dignissimos harum pariatur praesentium quaerat ipsam id, adipisci doloribus voluptatem.
Dolores maiores deleniti minima maxime quidem praesentium officiis consequatur, fugit omnis accusantium cupiditate dolor delectus. Eveniet ratione accusamus, adipisci sit itaque repellendus beatae earum nulla fuga nesciunt quod repudiandae voluptate.
Dolorum, perspiciatis fuga voluptates quod nemo atque architecto totam exercitationem debitis harum repudiandae iste amet pariatur nostrum. Totam fugit a, ipsam asperiores voluptatem eos eveniet nemo veritatis repellendus impedit pariatur.
Similique praesentium natus placeat corporis eius ullam nam harum, consequatur architecto, dolorum, quas omnis vitae molestiae earum doloremque ab. Nihil molestias temporibus alias impedit ut in hic id aliquid reprehenderit?
Natus quibusdam quia ipsa saepe quaerat nam, placeat veritatis deleniti hic dolor, quisquam corporis repellendus rerum debitis odit libero rem dolorem error. Explicabo temporibus similique sint inventore earum. Cupiditate, nisi.
Vero nemo ipsam enim, ullam saepe sequi repellendus vitae facere! Labore maxime officiis, hic perspiciatis repellat ipsam, non, animi ad omnis eaque dolorem ab. Doloremque corporis ipsa harum temporibus itaque.
Accusamus rerum molestiae deleniti fugit iure porro dolorem est, dignissimos odit earum exercitationem sed dolor recusandae nesciunt nam doloremque similique, cum delectus fugiat temporibus vel voluptatum culpa, dicta ducimus. Ipsam!
Aliquam, repellendus voluptate doloribus quae error repudiandae a sequi qui obcaecati maiores, vel numquam. Officiis quia, rerum illum placeat eius quibusdam numquam ut magnam non consequuntur. Minima sapiente temporibus qui.
Reprehenderit pariatur sed modi expedita. Ratione eius libero, illum molestiae harum repellendus ex dolores minima ut facilis, modi fuga omnis non, tempore eveniet odit voluptatem blanditiis adipisci unde! Necessitatibus, odit.
Sequi debitis doloremque similique doloribus quasi ea molestias nisi qui corrupti temporibus, voluptates aut tempora, ipsam omnis architecto nobis exercitationem nostrum natus? Distinctio esse soluta veritatis! Molestiae vero libero alias?
Ducimus nostrum odit quas quaerat amet ipsa asperiores impedit laboriosam, fugiat maiores, harum dignissimos tenetur molestias distinctio porro, error quae eum dolores suscipit tempora. Eos facilis iure quibusdam nostrum quidem!
Quasi corporis architecto labore odio rem suscipit illo, totam minima soluta dolore facere laboriosam eaque doloremque iusto doloribus quae tenetur. Odit earum, consequuntur explicabo debitis sit quidem doloribus beatae amet.
Harum similique, dolorum facilis, ex corrupti sed reprehenderit corporis itaque sequi enim accusamus omnis atque saepe voluptas dignissimos! Qui autem placeat consequuntur, rem debitis magni ex iusto animi culpa asperiores?
Accusantium laudantium dignissimos fugit ipsum veritatis quasi modi dolore assumenda distinctio debitis dolorum consequatur sequi quas numquam sint saepe, animi unde laborum, sapiente quisquam delectus! Fugit quod quam blanditiis delectus!
Iure recusandae laborum, illo cumque aliquid sint nobis similique temporibus sapiente ullam suscipit dicta voluptates ex animi, rem atque, aut eius pariatur. Sequi nihil eum optio temporibus quidem omnis deserunt?
Deleniti pariatur iste reiciendis saepe harum voluptate? Tenetur sapiente explicabo provident repellendus minus est, nobis rerum voluptates obcaecati pariatur laudantium iure amet error dolorum sunt a? Eligendi delectus at illum?
Nostrum quasi porro odit, veritatis error qui quod autem. Nemo distinctio dolores inventore dignissimos, nam, aut ullam cum rerum, molestias quos ad labore eum. Id exercitationem voluptate beatae impedit asperiores.
Iusto, voluptatum inventore adipisci animi numquam rerum. Odit tempore in at libero tenetur veritatis, ipsum odio suscipit deserunt quam quas harum adipisci molestias error, ullam numquam quasi rem quae illo?
Inventore repudiandae, esse molestiae voluptatum quisquam ducimus temporibus nostrum maiores? Similique, dignissimos illo cum vero quam voluptas in non perspiciatis adipisci qui optio unde dolorem voluptatibus sunt, facere animi esse?
Cumque deserunt eaque dignissimos repellat neque iste rerum illo numquam dicta, suscipit voluptatibus quam accusamus nemo provident eum voluptas nobis, sapiente alias quae consectetur! Error dignissimos voluptate atque consequatur quisquam?
Facilis voluptas delectus, eum commodi illum quis eveniet tenetur numquam repellat magni quae veniam beatae pariatur doloribus modi accusantium fugit saepe. Minima, consequatur minus. Praesentium consequatur excepturi veritatis nam quidem?
Quisquam nihil rerum quasi atque, corporis recusandae vel architecto dignissimos sint dolorum quod! Eius, at expedita aliquid deserunt ipsum a consectetur ratione sequi, obcaecati, vero quod quia magnam laboriosam magni.
Temporibus qui ipsum ullam. Delectus esse repellendus voluptatibus vero repellat nihil qui amet mollitia unde sint, maiores explicabo dolores doloribus doloremque sit est deserunt laudantium accusamus architecto nostrum! Expedita, unde!
Numquam voluptas suscipit modi aut quisquam fuga hic et ullam, magni harum id non voluptate autem enim ab iste aliquid ipsam qui ipsa, deleniti ratione, vitae nobis nam reprehenderit? Saepe!
Ducimus officiis vel illo et laborum quos ad beatae possimus optio dolore at tempora impedit repudiandae cum, placeat accusamus nulla quae consectetur, labore velit, debitis nobis quo aliquam mollitia. Accusantium!
Eveniet accusamus voluptatibus sapiente! Vero aspernatur molestiae ipsa pariatur quis! Amet excepturi illo, vero, tenetur reprehenderit consectetur commodi, ea aliquam velit modi dolores quasi. Accusamus dolorum earum a inventore odio?
Excepturi, voluptas? Soluta minima libero deserunt accusamus asperiores, iure modi molestias reprehenderit architecto quisquam? Quibusdam sint rem, fugiat eius provident eligendi veniam repellat odit nam velit sed deleniti, esse hic?
Culpa eos cupiditate provident ipsa quia iure eum minima, perspiciatis amet error voluptatem quas neque consequuntur, ipsam corporis at vitae autem, odio voluptatum explicabo nulla. Animi sit fugit nesciunt nostrum.
Cum, accusantium inventore similique vero non tempore ea animi delectus laboriosam nemo ullam doloremque architecto quasi iste, nihil eveniet assumenda minima itaque alias ut! Vel tenetur adipisci ea atque totam.
Expedita, velit commodi odio officia itaque fugit harum sapiente fuga dolorum sequi atque non nihil nisi dolorem laboriosam minima suscipit accusamus deserunt? Dolore, earum explicabo perspiciatis harum illo tempora laudantium!
Pariatur, aperiam neque dolorem, accusamus atque in deserunt, inventore id voluptatem minima obcaecati earum aliquid beatae harum placeat voluptates quisquam hic! Molestias mollitia repudiandae optio, incidunt nisi architecto corporis exercitationem?
Labore quos non rem doloremque commodi adipisci odit aliquid. Alias sed iusto quae dolor veritatis pariatur, dignissimos ex, explicabo modi, facilis iste inventore nam dolore rem aspernatur dolorum consectetur qui!
Magni labore, dolorem quam in ipsam pariatur ut ipsa blanditiis expedita laborum aspernatur omnis sint animi quis tenetur provident reiciendis consectetur cum temporibus repellendus rem mollitia? Non quibusdam aliquam fugiat!
Hic molestias animi repellendus nemo! Eos doloremque aut officia? Eius veritatis architecto debitis at, placeat eos sequi tenetur delectus, modi temporibus velit sit corrupti rem expedita magnam voluptatibus pariatur assumenda.
Deleniti esse eveniet perferendis in aliquid aliquam tenetur sunt aut atque possimus cupiditate odit reprehenderit quas quia sed quos, laudantium molestiae laborum! Corrupti, porro veniam? Officiis consequuntur est minima eius!
Est alias fugit id deserunt distinctio optio iure quos, repellendus molestias maxime eveniet libero, iste eos cumque ex quod accusamus consequatur voluptatem porro omnis culpa, autem tenetur asperiores. Distinctio, corporis.
Cumque officia at totam ex quod nobis voluptatem dolores, unde obcaecati? Deleniti nesciunt aspernatur sed praesentium deserunt odio quaerat exercitationem, soluta eos, tempore repellendus quae? Nostrum omnis excepturi nobis distinctio!
Amet laborum quis ullam facilis dolorem adipisci. Neque accusamus quos atque soluta aut quidem dolores et omnis, ullam dicta molestias optio ipsam, quisquam excepturi aliquid natus nisi quibusdam iusto tempore?
Neque illo ullam vitae a, quae doloribus natus rem sint assumenda in optio! Voluptate reiciendis, officiis at maxime quam perspiciatis soluta ab porro consequuntur alias aliquam voluptas qui sed vel.
Optio numquam itaque veniam. Illum facere repudiandae accusantium recusandae impedit. Ab voluptate eos aut exercitationem tenetur veritatis hic cumque voluptatem consequatur ducimus. Natus sunt unde voluptatibus dicta magnam voluptatem quo.
Dignissimos omnis ullam debitis similique totam excepturi asperiores molestias illum earum. Repellat nobis dignissimos, delectus quas accusantium non dolorum quidem maiores illo exercitationem amet ut doloribus sint magni fuga blanditiis.
Numquam error beatae corrupti molestiae tenetur mollitia excepturi. Dignissimos, quo. Provident aliquam nesciunt placeat velit nobis voluptatum porro iusto, veritatis saepe, soluta dicta possimus consequuntur dignissimos corporis, labore autem asperiores.
Minus aliquid sequi commodi, nobis ratione nesciunt eum a odit obcaecati corporis. Quisquam, dolor rerum! Officiis cupiditate id, ullam magni quibusdam perspiciatis tenetur. Quidem veritatis tenetur, non quo id unde.
Corrupti ipsa placeat perspiciatis praesentium adipisci possimus nostrum dolore quae. Aspernatur ipsum, odio reprehenderit vitae exercitationem nam quaerat fugit laudantium optio necessitatibus corrupti ad. In cumque officia dolores ullam commodi!
Est molestiae, ea voluptatibus excepturi commodi optio deleniti aut veniam voluptate omnis enim? At expedita repellendus unde quisquam repellat saepe eligendi nulla, qui doloribus iure hic alias suscipit autem facilis.
Officiis ad quibusdam quas id cupiditate alias molestiae eaque voluptatum sunt, dicta vel beatae et nesciunt, voluptatibus veniam maxime officia magni ducimus. A mollitia itaque dolorum aperiam earum in voluptates.
Autem aliquam delectus, quas reiciendis impedit maxime non, et minima error magni rerum suscipit facilis. Sint inventore optio quasi, esse aliquam consequatur mollitia quaerat necessitatibus perspiciatis praesentium placeat! Ea, corrupti.
Cum obcaecati error ipsum cupiditate harum at minima nulla nostrum? Saepe odit, iusto nemo pariatur neque impedit quibusdam quos sapiente similique. Rem, nostrum a atque iste ab impedit perspiciatis ipsum.
Odit dolore eveniet quod deleniti aut tenetur similique, officiis maxime fuga. Nisi iste dolores deserunt, ea praesentium est exercitationem illo? Deleniti ipsa perferendis accusantium asperiores fuga sint fugiat minima explicabo?
Expedita nam incidunt minima ullam adipisci. Unde asperiores mollitia vero deleniti alias ipsum quas provident nesciunt dolorum, ab earum nisi libero dignissimos perferendis aliquam quaerat, facere, exercitationem quam tempore porro?
Accusantium tempora praesentium quibusdam? Harum vitae consequatur accusamus neque aliquid, a ipsum, veniam repellat accusantium explicabo ipsa cupiditate temporibus magnam laboriosam sunt. Nobis optio unde voluptas facilis ratione consequuntur sequi?
Sapiente hic voluptate facilis eligendi? Nostrum nesciunt sequi aliquam, quod totam dicta consectetur ut sit ipsum quos adipisci id aliquid dolore minima commodi quae exercitationem suscipit maiores. Ullam, voluptates cum?
Rerum illo sit soluta doloremque molestias reiciendis debitis! Porro, aliquam. Placeat repellendus at eveniet, officiis reiciendis, soluta officia dolores quos sapiente consequatur esse libero atque repellat nisi voluptatem dicta maiores?
Facere inventore minima repellendus debitis commodi sint, molestias, ut eligendi doloremque itaque voluptas necessitatibus et illo odio soluta dolorum unde iste neque quas! Sit cumque enim recusandae fugit quam facere?
Facere dolor magnam ducimus itaque reiciendis saepe sunt minus aperiam quos illo! Iure quas tenetur rem, laborum esse molestias provident. Asperiores facilis iusto necessitatibus eum dicta veniam, numquam eligendi itaque?
Modi aliquid id corporis! Expedita reprehenderit cum numquam non incidunt temporibus sed libero! Ullam nam, magnam quas, deserunt dolore nulla consequuntur recusandae placeat iure, earum assumenda voluptatum aliquid corporis consectetur.
Nihil dolorum quos architecto ut quisquam eaque beatae, labore cupiditate quidem, unde enim. Assumenda dolor modi ipsam ullam blanditiis! Ipsa hic temporibus aut voluptas placeat voluptatem nihil quaerat corrupti corporis?
Dolor, nisi? Tenetur ab beatae, consequuntur minima, facere inventore, ducimus molestiae quam soluta reprehenderit fugit similique veniam quis maiores placeat illum debitis commodi aliquam. Quasi alias cupiditate qui blanditiis. Sunt.
Natus cumque maxime ipsum quia repellat eligendi id iusto totam quod odit voluptate, explicabo ducimus porro ad quaerat. Voluptate, impedit. Voluptatum, magni consequatur. Ipsum perferendis quia natus eos corrupti vero.
Cumque quidem quisquam explicabo reiciendis. Quidem rerum nihil harum, minima repellendus eos qui nostrum modi! Accusantium possimus ut esse! Nostrum voluptates placeat tempore delectus et omnis enim! Nostrum, nesciunt id.
Aspernatur doloremque consequatur optio distinctio maxime minima autem cupiditate recusandae voluptatem possimus ipsum culpa expedita fuga iste consequuntur at nemo, eligendi veniam aperiam fugiat ratione voluptate cumque molestias. Nobis, odio.
Unde, quam distinctio mollitia voluptas asperiores amet, ad sapiente blanditiis dicta dolores perferendis totam quod ipsa alias et sit! Sunt quaerat corporis et quo delectus temporibus consequatur sequi earum ipsam?
Aliquid necessitatibus odio voluptatem dolorem dolore accusamus nemo ipsam mollitia, dolorum voluptate consequatur facere earum recusandae error exercitationem eveniet, facilis porro quo optio dolores quibusdam. Aliquam expedita eius odit culpa.
Error numquam totam, blanditiis necessitatibus eaque laborum, explicabo itaque iusto eos, similique assumenda fugit quaerat nihil dignissimos architecto eius vel veniam quisquam eligendi asperiores eum minima. Deleniti, ut. Amet, nam!
Saepe obcaecati quaerat enim perferendis ab sunt aliquam, porro temporibus dicta totam recusandae, libero numquam cupiditate esse aliquid magni omnis impedit, officia neque placeat. Laudantium eos esse quasi possimus deserunt!
Doloribus quod fuga repellendus aliquid mollitia at qui voluptatibus sint, id possimus deleniti. Aliquid corporis magni voluptate ratione cumque? Libero voluptatem tempore accusamus aperiam dolorem numquam voluptas consectetur voluptate placeat?
Perspiciatis eligendi id enim animi aliquam repudiandae doloribus nobis tempora libero consequatur neque at recusandae possimus, fugit atque totam numquam iste natus voluptatum consequuntur saepe ad aliquid! Illo, porro sapiente!
Ab, qui perspiciatis, consequatur quibusdam necessitatibus eius atque harum, sed unde accusamus dicta non sit? Itaque, nostrum fuga impedit alias voluptate fugit ratione at consectetur vel tempora expedita, ullam vitae?
Culpa possimus dolorem maiores debitis necessitatibus? Modi tempore numquam harum dignissimos laboriosam commodi itaque qui minima natus ab facere totam sed est similique neque, dolore impedit, cupiditate laudantium. Labore, quasi?
Officia totam aut odit sequi iusto itaque ipsam, atque ducimus at fugit. Rerum veniam, cumque eaque nostrum eius consequuntur alias molestiae. Modi sapiente, quas laudantium nesciunt ad soluta nam. Recusandae.
Officiis, dolor magni aspernatur nulla libero dolorem provident, deserunt minima est nam corporis, vel deleniti obcaecati ex dolorum illum rem voluptates quaerat id! Ducimus error in animi. Asperiores, magni eius.
Exercitationem odio incidunt voluptate vero doloremque culpa ipsum sequi vitae rem commodi perferendis inventore impedit alias saepe atque, officia suscipit obcaecati ducimus quos architecto, aperiam provident porro. Ipsa, delectus omnis.
Ea aspernatur officiis labore iste quibusdam suscipit amet velit, facere consectetur architecto sunt reprehenderit vero expedita nesciunt totam nulla harum cupiditate, eveniet repellat voluptatibus. Ea ullam possimus nulla modi pariatur?
Laborum facere itaque atque illum in voluptas ullam numquam accusamus rerum, necessitatibus placeat libero omnis ad dolores nostrum eaque suscipit sunt dolor nemo similique reiciendis consectetur porro. Aliquid, dignissimos iusto.
Officia minima, quia esse fugiat omnis labore sequi aperiam eos, ad voluptatum maxime accusantium tempora odio, necessitatibus suscipit debitis sit nobis temporibus doloremque autem explicabo corrupti officiis? Minus, nobis molestias?
Nesciunt dolorum reiciendis tempore fugit assumenda similique dignissimos libero dolorem natus modi magni pariatur, enim sequi molestiae perferendis asperiores distinctio, sint perspiciatis ipsum est aperiam amet iure. Assumenda, maxime sapiente!
Quam, earum. Quod cumque dicta nesciunt velit ipsum, facilis ullam nam quibusdam tenetur placeat nostrum at alias autem molestiae rem corporis inventore illo sapiente saepe minima eligendi sint? Esse, nostrum!
Nisi, debitis quasi error distinctio officia, quaerat sit, recusandae velit dolorem voluptatum excepturi? Deserunt labore incidunt odio nostrum. Quae modi incidunt eos consequatur commodi ea, fuga maiores laudantium illo nihil!
Ipsam voluptatum labore quaerat autem perspiciatis velit dolores voluptatibus nesciunt? Ratione provident quos aliquam perferendis architecto rem impedit dolor placeat odio veritatis ab inventore quaerat, dicta corporis? Ducimus, eius temporibus?
Deleniti, suscipit? Illo molestiae culpa itaque saepe vitae aliquid, non et aliquam temporibus, debitis suscipit aperiam voluptate a voluptatum ratione fugit aut sed aspernatur voluptas cumque est nemo maxime. Explicabo.
Vitae perferendis sapiente sed, voluptas ex qui a asperiores quia voluptatum veniam? Illum aliquid accusantium deleniti laborum numquam eum nemo odio incidunt, perspiciatis delectus, ea et aut, eos nihil fugiat!
Ea, neque inventore consectetur totam ullam excepturi accusantium explicabo unde natus perferendis laborum incidunt, odio hic! Numquam eveniet error tempora, minima fugiat facere itaque non, laudantium ipsum ipsa autem esse.
Atque ex est recusandae consectetur similique beatae adipisci debitis quia amet, aspernatur excepturi neque porro et natus ea? Rem ad ipsa saepe quam eius expedita optio dolorum? Error, nemo facere?
Repudiandae quia culpa magnam laboriosam harum. Pariatur porro ab reprehenderit? Nihil, praesentium impedit doloribus magni cum, libero iste tempore amet harum voluptas voluptatum ex rem a placeat non dicta provident?
</body>
</html>