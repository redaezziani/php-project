<footer class="bg-green-900 text-white mt-auto">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-right">
            <!-- Contact Info -->
            <div>
                <h3 class="text-xl font-bold mb-4 text-amber-200">تواصل معنا</h3>
                <ul class="space-y-2">
                    <li>
                        <span class="text-amber-200">الهاتف:</span> 
                        <span dir="ltr">+212-500-000000</span>
                    </li>
                    <li>
                        <span class="text-amber-200">البريد الإلكتروني:</span> 
                        <span dir="ltr">contact@store.com</span>
                    </li>
                    <li>
                        <span class="text-amber-200">العنوان:</span>
                        شارع محمد الخامس، الرباط، المغرب
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-xl font-bold mb-4 text-amber-200">روابط سريعة</h3>
                <ul class="space-y-2">
                    <li><a href="/IHM/Produits/index.php" class="hover:text-amber-200">المنتجات</a></li>
                    <li><a href="/IHM/Categories/index.php" class="hover:text-amber-200">التصنيفات</a></li>
                    <li><a href="/IHM/Cart/index.php" class="hover:text-amber-200">سلة المشتريات</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="/IHM/Orders/index.php" class="hover:text-amber-200">طلباتي</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- About -->
            <div>
                <h3 class="text-xl font-bold mb-4 text-amber-200">عن المتجر</h3>
                <p class="mb-4">متجر المواد الغذائية الخاص بك لجميع احتياجاتك اليومية من المنتجات الطازجة والجودة العالية.</p>
                <div class="flex justify-end space-x-4 space-x-reverse">
                    <a href="#" class="hover:text-amber-200" title="فيسبوك">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path></svg>
                    </a>
                    <a href="#" class="hover:text-amber-200" title="تويتر">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                    </a>
                    <a href="#" class="hover:text-amber-200" title="إنستغرام">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16 4H8C5.79086 4 4 5.79086 4 8V16C4 18.2091 5.79086 20 8 20H16C18.2091 20 20 18.2091 20 16V8C20 5.79086 18.2091 4 16 4Z"></path><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-4 border-t border-green-800 text-center text-sm">
            <p>© <?= date('Y') ?> متجر المواد الغذائية. جميع الحقوق محفوظة</p>
        </div>
    </div>
</footer>
