<?php if (!defined('ABSPATH')) { exit; } ?>
<style>
    body {
        background: #ffffff !important;
    }

    .chaty-updates-form {
        width: 768px;
        padding: 70px 40px;
        box-shadow: 0px 20px 25px rgb(0 0 0 / 10%), 0px 10px 10px rgb(0 0 0 / 4%);
        display: flex;
        margin: 100px auto 0;
        font-family: Rubik, sans-serif;
    }
    .update-title {
        font-style: normal;
        font-weight: 500;
        font-size: 26px;
        line-height: 150%;
        align-items: center;
        color: #334155;
    }
    .updates-form-form-left {
        padding: 50px 20px 50px 0;
    }
    .updates-form-form-right p {
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 150%;
        position: relative;
        padding: 0 0 20px 0;
        color: #475569;
        margin: 40px 0;
    }
    .updates-form-form-right p:after {
        content: "";
        border: 1px solid #3C85F7;
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 90px;
    }
    .updates-form {
        display: flex;
    }
    .update-form-input {
        position: relative;
    }
    .update-form-input input {
        width: 320px;
        background: #F4F4F5;
        border: 1px solid #F4F4F5;
        box-sizing: border-box;
        border-radius: 4px;
        height: 40px;
        line-height: 40px;
        padding: 0 50px 0 40px;
        font-size: 13px;
        box-sizing: border-box;
        color: #334155;
    }
    .update-form-input .form-submit-btn {
        background: #3C85F7;
        border-radius: 4px;
        border: none;
        color: #fff;
        font-style: normal;
        font-weight: 500;
        font-size: 13px;
        line-height: 150%;
        height: 34px;
        padding: 0 10px;
        position: absolute;
        right: 3px;
        top: 3px;
        cursor: pointer;
    }
    .updates-form .form-cancel-btn.no {
        margin: 0 0 0 3px;
        background: transparent;
        border: none;
        color: #64748B;
        font-size: 13px;
        line-height: 40px;
        padding: 0 0 0 5px;
    }
    .updates-form .form-cancel-btn.no:hover {
        color: #334155;
    }
    .mail-icon {
        position: absolute;
        top: 8px;
        left: 10px;
    }
    .update-notice {
        margin: 50px 0 0 0;
        font-size: 12px;
        padding: 0 110px 0 0;
        line-height: 150%;
        color: #64748B;
    }
</style>
<div class="chaty-updates-form">
    <div class="updates-form-form-left">
        <svg width="261" height="243" viewBox="0 0 261 243" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.6418 144.346C10.6418 144.346 10.4222 143.702 10.0669 142.505" stroke="#69F0AE" stroke-width="9" stroke-miterlimit="10"/>
            <path opacity="0.6" d="M9.02692 138.772C1.17536 108.652 -23.303 -30.1614 222.082 56.4967" stroke="#3C85F7" stroke-width="9" stroke-miterlimit="10" stroke-dasharray="12.04 12.04"/>
            <path d="M223.919 57.1403L225.744 57.7839" stroke="#69F0AE" stroke-width="9" stroke-miterlimit="10"/>
            <path d="M195.688 164.197C195.69 157.688 193.254 151.414 188.857 146.6C184.46 141.787 178.418 138.782 171.915 138.173C165.411 137.563 158.913 139.394 153.693 143.306C148.472 147.218 144.905 152.93 143.689 159.325L63.0771 157.146L63.9621 170.945C63.9621 170.945 52.4674 197.37 112.88 204.411V231.12H182.721V186.712C186.665 184.412 189.936 181.123 192.211 177.174C194.486 173.224 195.684 168.75 195.688 164.197Z" fill="url(#paint0_linear)"/>
            <path d="M66.7297 171.524C66.7297 171.524 55.7227 196.83 113.577 203.581V229.169H180.463V161.4L65.8706 158.308L66.7297 171.524Z" fill="#F6B9AD"/>
            <path d="M100.158 65.922C98.4136 68.6639 95.6514 70.6067 92.4743 71.326C89.2972 72.0453 85.9635 71.4827 83.2014 69.7609L47.2896 46.1834C44.5376 44.4457 42.5876 41.6936 41.8657 38.5282C41.1437 35.3627 41.7085 32.0413 43.4365 29.2894C45.1804 26.551 47.9403 24.6107 51.1143 23.8915C54.2883 23.1724 57.6189 23.7327 60.3799 25.4504L96.2916 49.028C99.0458 50.7635 100.998 53.5147 101.723 56.6803C102.447 59.8459 101.885 63.1685 100.158 65.922Z" fill="url(#paint1_linear)"/>
            <path d="M98.1685 64.6316C96.5474 67.1803 93.9798 68.9862 91.0267 69.6548C88.0735 70.3235 84.9747 69.8005 82.4073 68.2002L49.0148 46.2831C46.4567 44.668 44.6441 42.1098 43.973 39.1675C43.3019 36.2251 43.8268 33.1378 45.433 30.5797C47.0541 28.0311 49.6216 26.2252 52.5748 25.5565C55.528 24.8879 58.6267 25.4108 61.1942 27.0111L94.5803 48.9314C97.1385 50.5455 98.9518 53.1026 99.6241 56.0442C100.296 58.9859 99.7731 62.0731 98.1685 64.6316Z" fill="#F6B9AD"/>
            <path d="M167.763 190.085C181.635 190.085 192.881 178.88 192.881 165.059C192.881 151.238 181.635 140.034 167.763 140.034C153.891 140.034 142.645 151.238 142.645 165.059C142.645 178.88 153.891 190.085 167.763 190.085Z" fill="#F6B9AD"/>
            <path d="M163.242 0H67.0204C64.8353 0 63.064 1.76486 63.064 3.94193V183.327C63.064 185.504 64.8353 187.269 67.0204 187.269H163.242C165.427 187.269 167.198 185.504 167.198 183.327V3.94193C167.198 1.76486 165.427 0 163.242 0Z" fill="url(#paint2_linear)"/>
            <path d="M161.384 2.53249H68.8776C66.4445 2.53249 64.4722 4.49761 64.4722 6.92171V180.354C64.4722 182.778 66.4445 184.743 68.8776 184.743H161.384C163.817 184.743 165.79 182.778 165.79 180.354V6.92171C165.79 4.49761 163.817 2.53249 161.384 2.53249Z" fill="white"/>
            <path opacity="0.9" d="M140.584 7.0987C140.291 9.03104 139.314 10.7951 137.829 12.0717C136.344 13.3484 134.449 14.0533 132.487 14.059H97.4962C95.5353 14.0525 93.6414 13.3472 92.1569 12.0707C90.6725 10.7941 89.6954 9.0305 89.4024 7.0987H70.8635C70.3581 7.09827 69.8576 7.19709 69.3905 7.38951C68.9234 7.58192 68.499 7.86415 68.1415 8.22008C67.7839 8.576 67.5003 8.99864 67.3068 9.46383C67.1133 9.92902 67.0137 10.4277 67.0137 10.9312V176.331C67.0132 176.835 67.1124 177.334 67.3056 177.799C67.4987 178.264 67.7819 178.687 68.1392 179.044C68.4964 179.4 68.9206 179.682 69.3875 179.875C69.8544 180.068 70.3549 180.167 70.8603 180.167H159.401C159.906 180.168 160.407 180.069 160.874 179.876C161.341 179.684 161.766 179.402 162.123 179.046C162.481 178.69 162.764 178.267 162.958 177.802C163.151 177.337 163.251 176.838 163.251 176.335V10.9344C163.251 10.4309 163.152 9.93216 162.959 9.4668C162.766 9.00145 162.483 8.57858 162.125 8.22235C161.768 7.86613 161.344 7.58354 160.877 7.39074C160.41 7.19793 159.91 7.0987 159.404 7.0987H140.584Z" fill="#3C85F7"/>
            <path d="M125.919 9.28044H104.631C104.275 9.28044 103.985 9.56858 103.985 9.92402V10.0431C103.985 10.3985 104.275 10.6867 104.631 10.6867H125.919C126.275 10.6867 126.565 10.3985 126.565 10.0431V9.92402C126.565 9.56858 126.275 9.28044 125.919 9.28044Z" fill="#DBDBDB"/>
            <path d="M131.36 10.6867C131.828 10.6867 132.207 10.3092 132.207 9.84356C132.207 9.37794 131.828 9.00047 131.36 9.00047C130.893 9.00047 130.514 9.37794 130.514 9.84356C130.514 10.3092 130.893 10.6867 131.36 10.6867Z" fill="#DBDBDB"/>
            <path d="M184.002 169.471C176.713 171.151 171.71 166.414 170.024 159.154L153.275 97.1903C152.474 93.6895 153.096 90.0153 155.005 86.9699C156.915 83.9246 159.956 81.7557 163.464 80.9367C166.978 80.1391 170.666 80.7591 173.722 82.6612C176.779 84.5634 178.956 87.5933 179.778 91.0892L193.446 154.643C195.132 161.915 191.289 167.791 184.002 169.471Z" fill="url(#paint3_linear)"/>
            <path d="M177.898 92.6093C176.477 86.4812 170.339 82.6613 164.188 84.0772C158.038 85.4931 154.204 91.6086 155.625 97.7367L171.027 164.152C172.448 170.28 178.586 174.1 184.737 172.684C190.887 171.268 194.721 165.152 193.3 159.024L177.898 92.6093Z" fill="#F6B9AD"/>
            <path d="M192.778 220.796H97.1768V243H192.778V220.796Z" fill="url(#paint4_linear)"/>
            <path d="M190.194 223.371H99.7607V240.747H190.194V223.371Z" fill="#FF8976"/>
            <path d="M136.46 58.6881L129.051 53.1308L132.281 48.9475L126.891 44.7321L123.522 48.9862L115.903 43.2744C115.613 43.0574 115.26 42.9401 114.897 42.9401C114.534 42.9401 114.181 43.0574 113.891 43.2744L93.695 58.2537C92.9296 58.8973 92.2126 59.6342 92.2061 61.3204H92.1835L92.1318 84.3992V85.4257C92.131 85.7562 92.1955 86.0835 92.3216 86.3892C92.4478 86.6948 92.6331 86.9727 92.867 87.207C93.101 87.4412 93.3789 87.6273 93.685 87.7545C93.9911 87.8818 94.3194 87.9477 94.6511 87.9485H95.6458L114.795 87.9904L134.942 88.0354C135.274 88.0363 135.603 87.972 135.909 87.8463C136.216 87.7207 136.495 87.536 136.73 87.3029C136.965 87.0699 137.152 86.7929 137.28 86.488C137.407 86.183 137.474 85.8559 137.474 85.5255L137.529 61.462H137.571C137.571 59.7726 137.226 59.3317 136.46 58.6881Z" fill="url(#paint5_linear)"/>
            <path d="M135.582 61.8127C135.582 60.2746 135.259 59.882 134.568 59.2867L115.812 45.2341C115.547 45.0359 115.225 44.9288 114.893 44.9288C114.562 44.9288 114.24 45.0359 113.975 45.2341L95.5488 58.9037C95.1056 59.2192 94.749 59.6406 94.512 60.1293C94.275 60.6179 94.1651 61.1581 94.1923 61.7001H94.173L94.1245 82.7581V83.6945C94.1237 84.3033 94.3653 84.8875 94.7965 85.3189C95.2277 85.7503 95.8131 85.9936 96.4241 85.9953H97.3317L114.805 86.0339L133.185 86.0757C133.796 86.0766 134.383 85.8358 134.816 85.4062C135.249 84.9766 135.493 84.3934 135.495 83.7846L135.543 61.8288L135.582 61.8127Z" fill="url(#paint6_linear)"/>
            <path d="M94.5117 61.7844L135.152 61.8763L135.101 84.1538C135.101 84.3304 135.065 84.5052 134.997 84.6683C134.929 84.8313 134.829 84.9794 134.703 85.104C134.578 85.2286 134.429 85.3274 134.265 85.3946C134.101 85.4618 133.925 85.4962 133.748 85.4958L95.9598 85.4104C95.5649 85.4095 95.1865 85.2523 94.9079 84.9735C94.6293 84.6947 94.4733 84.317 94.4742 83.9236L94.5246 61.7844L94.5117 61.7844Z" fill="#6C63FF"/>
            <path opacity="0.2" d="M94.5117 61.7844L135.152 61.8763L135.101 84.1538C135.101 84.3304 135.065 84.5052 134.997 84.6683C134.929 84.8313 134.829 84.9794 134.703 85.104C134.578 85.2286 134.429 85.3274 134.265 85.3946C134.101 85.4618 133.925 85.4962 133.748 85.4958L95.9598 85.4104C95.5649 85.4095 95.1865 85.2523 94.9079 84.9735C94.6293 84.6947 94.4733 84.317 94.4742 83.9236L94.5246 61.7844L94.5117 61.7844Z" fill="white"/>
            <path d="M94.5154 61.7773L94.4702 82.24C94.4694 82.6528 94.5502 83.0618 94.7079 83.4436C94.8657 83.8253 95.0975 84.1724 95.3899 84.4649C95.6823 84.7575 96.0297 84.9897 96.4122 85.1485C96.7947 85.3073 97.2049 85.3894 97.6192 85.3903L131.948 85.4675" fill="#F5F5F5"/>
            <path d="M135.158 61.8674L135.113 82.3429C135.112 82.7558 135.03 83.1645 134.871 83.5456C134.711 83.9267 134.478 84.2728 134.184 84.5641C133.891 84.8555 133.542 85.0863 133.159 85.2435C132.776 85.4008 132.366 85.4813 131.951 85.4804L97.6221 85.4032" fill="white"/>
            <path d="M113.972 45.7682L95.885 59.0582C95.4505 59.3626 95.1004 59.7715 94.8672 60.2469C94.634 60.7223 94.5252 61.2488 94.5511 61.7773L135.211 61.906C135.211 60.4097 134.888 60.0171 134.216 59.4508L115.79 45.7747C115.528 45.5777 115.21 45.4705 114.882 45.4694C114.554 45.4682 114.235 45.5731 113.972 45.7682Z" fill="#6C63FF"/>
            <path opacity="0.2" d="M113.972 45.7682L95.885 59.0582C95.4505 59.3626 95.1004 59.7715 94.8672 60.2469C94.634 60.7223 94.5252 61.2488 94.5511 61.7773L135.211 61.906C135.211 60.4097 134.888 60.0171 134.216 59.4508L115.79 45.7747C115.528 45.5777 115.21 45.4705 114.882 45.4694C114.554 45.4682 114.235 45.5731 113.972 45.7682Z" fill="black"/>
            <path d="M130.757 50.3924L125.838 46.5502L113.949 61.5521L107.561 56.6158L103.559 61.684L112.858 68.7602L115.164 70.604L130.757 50.3924Z" fill="url(#paint7_linear)"/>
            <path d="M130.395 50.299L125.938 46.8945L114.126 62.2407L107.567 57.2304L104.146 61.6711L112.935 68.3772L115.189 70.0602L130.395 50.299Z" fill="white"/>
            <path d="M92.4451 99.6939C90.7137 102.415 87.972 104.343 84.8186 105.057C81.6652 105.771 78.3563 105.213 75.6148 103.504L45.0935 83.7041C42.3622 81.9791 40.427 79.2474 39.7105 76.1056C38.994 72.9638 39.5545 69.6671 41.2694 66.9356C43.0008 64.2144 45.7426 62.2863 48.896 61.5724C52.0494 60.8586 55.3582 61.417 58.0998 63.1256L88.621 82.9254C91.3523 84.6504 93.2875 87.3821 94.004 90.5239C94.7205 93.6657 94.16 96.9624 92.4451 99.6939Z" fill="url(#paint8_linear)"/>
            <path d="M90.8236 98.6513C89.2025 101.2 86.6349 103.006 83.6817 103.675C80.7285 104.343 77.6298 103.82 75.0623 102.22L46.4757 83.6752C43.9176 82.06 42.1051 79.5019 41.434 76.5595C40.7628 73.6172 41.2877 70.5298 42.8939 67.9718C44.515 65.4231 47.0826 63.6172 50.0358 62.9486C52.9889 62.2799 56.0877 62.8028 58.6551 64.4031L87.2418 82.9479C89.7998 84.5631 91.6124 87.1212 92.2835 90.0635C92.9546 93.0059 92.4298 96.0933 90.8236 98.6513Z" fill="#F6B9AD"/>
            <path d="M86.218 132.394C84.4951 135.104 81.7661 137.023 78.6271 137.734C75.488 138.445 72.1942 137.889 69.4652 136.188L45.3776 120.662C42.6583 118.945 40.7314 116.226 40.0179 113.099C39.3044 109.971 39.8623 106.689 41.5697 103.97C43.2925 101.261 46.0216 99.3413 49.1606 98.6305C52.2996 97.9196 55.5935 98.4754 58.3225 100.177L82.4068 115.706C85.1258 117.422 87.0527 120.14 87.7668 123.267C88.4809 126.394 87.924 129.675 86.218 132.394Z" fill="url(#paint9_linear)"/>
            <path d="M80.1721 165.262C78.434 167.996 75.6803 169.934 72.5127 170.651C69.3452 171.369 66.0214 170.808 63.2676 169.091L48.3493 158.247C45.6051 156.515 43.6604 153.771 42.9402 150.616C42.2201 147.46 42.7829 144.148 44.5059 141.404C46.2461 138.672 49.0007 136.737 52.1681 136.022C55.3355 135.307 58.6582 135.87 61.4105 137.588L76.3287 148.429C79.0703 150.161 81.0131 152.902 81.7331 156.056C82.4532 159.21 81.892 162.519 80.1721 165.262Z" fill="url(#paint10_linear)"/>
            <path d="M78.9674 164.454C77.3463 167.003 74.7787 168.809 71.8255 169.477C68.8723 170.146 65.7736 169.623 63.2061 168.023L49.2955 157.912C46.7375 156.297 44.9249 153.739 44.2538 150.797C43.5827 147.854 44.1075 144.767 45.7137 142.209C47.3348 139.66 49.9024 137.854 52.8556 137.186C55.8088 136.517 58.9075 137.04 61.475 138.64L75.3856 148.751C77.9436 150.366 79.7562 152.924 80.4273 155.866C81.0984 158.809 80.5736 161.896 78.9674 164.454Z" fill="#F6B9AD"/>
            <path d="M148.074 104.122H82.187V123.751H148.074V104.122Z" fill="url(#paint11_linear)"/>
            <path d="M145.814 106.052H84.4482V120.211H145.814V106.052Z" fill="#69F0AE"/>
            <path d="M259.84 69.0305L261 69.2107L260.819 69.0401H260.916L260.764 68.9886L239.741 49.1245L234.638 57.1468L230.395 64.4643L232.361 64.77L231.489 68.7344L259.84 69.0305Z" fill="url(#paint12_linear)"/>
            <path d="M235.52 57.5651L231.645 64.2615L260.066 68.8728L241.76 56.1717L235.52 57.5651Z" fill="#6C63FF"/>
            <path opacity="0.2" d="M235.52 57.5651L231.645 64.2615L260.066 68.8728L241.76 56.1717L235.52 57.5651Z" fill="black"/>
            <path d="M240.188 50.2283L260.067 68.8728L235.521 57.5651L240.188 50.2283Z" fill="#6C63FF"/>
            <path d="M232.691 68.197L259.989 68.7151L234.506 59.6985L232.691 68.197Z" fill="#6C63FF"/>
            <path d="M84.8936 131.554C83.2725 134.103 80.7049 135.909 77.7517 136.578C74.7985 137.246 71.6998 136.723 69.1323 135.123L46.4723 120.514C43.9142 118.899 42.1017 116.34 41.4305 113.398C40.7594 110.456 41.2843 107.368 42.8905 104.81C44.509 102.259 47.0754 100.45 50.0288 99.7781C52.9822 99.1063 56.0823 99.6268 58.6517 101.226L81.3118 115.835C83.8743 117.45 85.69 120.011 86.3613 122.958C87.0326 125.904 86.5049 128.995 84.8936 131.554Z" fill="#F6B9AD"/>
            <defs>
                <linearGradient id="paint0_linear" x1="129.387" y1="231.132" x2="129.387" y2="138.061" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint1_linear" x1="24916.3" y1="14199" x2="24916.3" y2="7023.87" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint2_linear" x1="56806.2" y1="122524" x2="56806.2" y2="13540.6" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint3_linear" x1="29845.7" y1="53620" x2="29845.7" y2="28883.9" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint4_linear" x1="61020.9" y1="18572.5" x2="61020.9" y2="17040.4" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint5_linear" x1="114.85" y1="88.0322" x2="114.85" y2="42.9365" gradientUnits="userSpaceOnUse">
                    <stop stop-opacity="0.12"/>
                    <stop offset="0.55" stop-opacity="0.09"/>
                    <stop offset="1" stop-opacity="0.02"/>
                </linearGradient>
                <linearGradient id="paint6_linear" x1="22673.8" y1="14029.1" x2="22673.8" y2="8768.08" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint7_linear" x1="15019" y1="7079.44" x2="15023" y2="5279.5" gradientUnits="userSpaceOnUse">
                    <stop stop-opacity="0.12"/>
                    <stop offset="0.55" stop-opacity="0.09"/>
                    <stop offset="1" stop-opacity="0.02"/>
                </linearGradient>
                <linearGradient id="paint8_linear" x1="21783.4" y1="17691.5" x2="21783.4" y2="11649.7" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint9_linear" x1="18747.1" y1="20007.7" x2="18747.1" y2="15107.8" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint10_linear" x1="15098.2" y1="21407.6" x2="15098.2" y2="17548.1" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
                <linearGradient id="paint11_linear" x1="22956.9" y1="7652.93" x2="22956.9" y2="6455.55" gradientUnits="userSpaceOnUse">
                    <stop stop-opacity="0.12"/>
                    <stop offset="0.55" stop-opacity="0.09"/>
                    <stop offset="1" stop-opacity="0.02"/>
                </linearGradient>
                <linearGradient id="paint12_linear" x1="21383.9" y1="21299.4" x2="22083.3" y2="20191.7" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#808080" stop-opacity="0.25"/>
                    <stop offset="0.54" stop-color="#808080" stop-opacity="0.12"/>
                    <stop offset="1" stop-color="#808080" stop-opacity="0.1"/>
                </linearGradient>
            </defs>
        </svg>
    </div>
    <div class="updates-form-form-right">
        <div class="update-title">Be first</div>
        <p>Be among the first to know about our latest features & what we're working on. Plus, insider offer & flash sales.</p>
        <div class="updates-form">
            <div class="update-form-input">
                <div class="mail-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0" mask-type="alpha" maskUnits="userSpaceOnUse" x="2" y="4" width="20" height="16">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM12 13L4 8V18H20V8L12 13Z" fill="white"/>
                        </mask>
                        <g mask="url(#mask0)">
                            <rect width="24" height="24" fill="#94A3B8"/>
                        </g>
                    </svg>
                </div>
                <input id="chaty_update_email" autocomplete="off" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="Email address">
                <button href="javascript:;" class="form-submit-btn yes">Sign Up</button>
            </div>
            <div class="update-form-skip-button">
                <button href="javascript:;" class="form-cancel-btn no">Skip</button>
            </div>
        </div>
        <div class="update-notice">
            You can remove yourself from the list whenever you want, no strings attached 😛
        </div>
        <input type="hidden" id="folder_update_status" value="<?php echo wp_create_nonce("folder_update_status") ?>">
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $(document).on("click", ".updates-form button", function () {
            var updateStatus = 0;
            if ($(this).hasClass("yes")) {
                updateStatus = 1;
            }
            $(".updates-form button").attr("disabled", true);
            $.ajax({
                url: ajaxurl,
                data: "action=folder_update_status&status=" + updateStatus + "&nonce=" + $("#folder_update_status").val() + "&email=" + $("#chaty_update_email").val(),
                type: 'post',
                cache: false,
                success: function () {
                    window.location.reload();
                }
            })
        });
    });
</script>