import {AdvColorControl} from "../0-adv-components/components.jsx";
import {IconListPopupHook} from "../0-adv-components/icon-class.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, AlignmentToolbar, URLInput } = wpBlockEditor;
    const { BaseControl, PanelBody, RangeControl, SelectControl, TextControl, Button } = wpComponents;
    const { times } = lodash;

    const previewImageData = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAfQAAAH0CAYAAADL1t+KAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAGVNJREFUeNrs3VuMXPV9wPEzs+sLDmu7BhIMNgWiUAtK4whQlRdLBUQiQOpD8oaUKqoqJZXymjyESijKU5QHpChPgYgkjSL1oYoipEo0JSoqQkSxlKiCchEFAgZsAnFtxxtfdqbzW3yc47Pn8j8zs8vuzucjjfYyOzNnZw3f8/+fW5YBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbT2+9L+D8/HzPnwmAdWR4/vz52Q76KM4bboUCAMaJfvGLtVgBWPWgXoh4b70sDwCsdsCbjOI+XI0FmGpACyPw3pReW+AB2KgBT/6ZaYzgpxbMlm3dvQ7f763VMgPAlAI+nOB7F++bJOwTxTFhRN4W7F6H5RFyANZr1IcTfj3xiH3sSDZsG08Ndq9D1MUcgPUc9WHL58PEn7+o67b2zqEcI+S9hJD3Joi52AOwViPxlBH3sCHmww6hX/46dbTeKYY1MW8Kc919qVEXcwDWY9RTR+TFj12j3ynsyUFMjHmvIexd7mt7LQBYD5FPCXjX+8aK+nxizJumwtsCXfn5ddddN/e1r31t14EDB3bv2bNn99atW3f0+/35wWDwwVIPh3G7+Lr51/ERANZKr9eL2zA+hqWlpfNnz549c/z48ROHDx9+75FHHvm/V155ZdAQ6vzzQcIKQ6/wsRz2XtPMQeuot2JknjKNXv66n39+3333bf3mN795yzXXXHP7jh077hg9/w2jkP/56LZndP82/3QAWOfOjAafJ0a3I6Owv7C4uPjCKOj/+f3vf//lRx999A8VMR82fK9tJWDFaL1uZ7nehCPz8ui8XxP3/re//e1dDzzwwN/u3r3770bPe4d/DwBsJqOR+yvvvPPOd5566qlffOELX3izMCKvCnjTfVnWMgVfFfXeBCPzqhF5+bYc+Jdffvmu/fv3/9Pc3NytWeI0PwBsQOdHYX/1hRde+PrBgwefbBiRF8PeNJJPjno/MeZZYrwvuf3gBz+4+ve///03rr/++n8dxfxTYg7AJjc/6t0nbrnlln85duzYQ9/61rf2jr43d+FWbGRtO7PqTdcruly+4Nlc1dL0+/260XnbqPzignz+85/f/tWvfvXrH/nIRx7o9Xrb/Y0BmCXbt2+/6eabb1549tlnn3799dcHpX6WOzusGEC36eU7kjcFvWnHt9YR+pe+9KXLR2slf79z585/HMV8pz8rALMmBrPbtm07cPfddy+++uqr//Piiy+e7zBYHtaEvVcegOdRTw166si8v7Cw0H/sscfuueqqq/5hbm7uOn9SAGY46ltHI/U9o5H62z/84Q//t3Qced0ovO7QtMpztuRB77ctS8PXlVH/4he/uDCK+d1btmz5K39KAGbd1q1b//K666678/7771/IVm4rr9uuXjV4brQi6BWHqrVtP79kIT772c/uG62N/HXCygIAzIJ+nHfl3nvvvTar3wGu+HXWEPMVo/R857jUvc6bThpzyfd37ty50O/39/r7AcDF6F69Z8+ehcJgd1ga+C6V4p4fzlbu8DBphF7aBb5urSBrGqFv2bIlTuH6Z/58APCBubm53du2bbssWznVXtXSphF67Si9X/NDWU28my6ksrwwS0tLLqICACWjPvaz6m3k/cTO9ho6PfaJXmoXxMVTAGClCxccK065D7I/Ta+3DZ5b49pvCHZTyOuG/UboAFBhMBg0HSnWda/2FffPJzygbi+7yr3di5c8BQAuCXpxhN7L0sNebmvxxDPDphF6ylpB5Ry/KXcAWKnUx6Ydz8fS7zKcb4j5xTWJ0pQCAPCnoLeNwjufUGZF0GuufZ4S+k4vCAAzGvS6eGdZ+17svZYe97pMufc6LDAAkD4wnrjD/QlevG5HOQBg8sg3dbXXNeipu84LOQA0SJjBTtluXnt/v+uQvo2d4gCgdSQ+1o5vTVwRDQA+nKhPVX+Mheit9UICwIyvAHS/HjoAsPEIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoACDoAICgAwCCDgAIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoACDoAICgAwCCDgAIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoAICgA4CgAwCCDgAIOgAg6AAg6ACAoAMAgg4ACDoACDoAIOgAgKADAIIOAIIOAAg6ACDoAICgA4CgAwCCDgAIOgAg6AAg6ACAoAMAgg4ACDoACDoAIOgAgKADAIIOAIIOAAg6ACDoAICgA4CgAwCCDgB8WOa9BeM7efJkdurUKW8EsGa2bt2aXXHFFd4IBH2aIuZvvfWWNwJYMwsLC4JOJVPuACDoAICgAwCCDgAIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoACDoAMB6Nu8tGN/evXuXbwBghA4ACDoAIOgAIOgAgKADAIIOAAg6AAg6ACDoAICgAwCCDgCCDgAIOgAg6ACAoAOAoAMAgg4ACDoAIOgAIOgAgKADAIIOAAg6AAg6ACDoAMCamPcWwMZz9uzZ7L333vNGTMnevXu9CQg6sPbOnDmTvfXWW94IQYeLTLkDgKADAIIOAAg6ACDoALBpzOxe7m+88Ua2uLjoX8AmsW/fvmzHjh3eCEDQZ03EfPfu3dlll122aX/HOE45bjfddNOm/lu++eab2dLSkv+aAUGfVRHzhYWF5RgcO3YsO3nyZPJj5+bmllcIrrjiinX7+506dWr5Y/yO0xQnNTl69GinGY6tW7cuL8dqvF/xtwAQ9BkXcXr++efHGuEdP358+fbxj398Zt6v06dPZy+99NJY71fMFsRK0/XXX++/PIApm/md4uJsW5NM1+ZRnxVvv/32RO9XRD1WCgAQ9KmP0Cc1SzvXTWNbte3dAIIOAFRwcZaS/fv3N+75nu853iamlWPv6yaph1q1vWYsbyx3F1XPGTusjbvTWtVjY+YiDg8cR937t9n32AcQ9CmJyDXtNR1XuUqxbdu21is4xc+kiL3DYy/xOuPs5V31nKnLUyV2ditvvphkaj3l/ZtlsSJo5QYQ9JaRYbjmmmtW3NflcpURs/ywsaKIVKw0RPwiWikxjpWIeK58BFweWUeYu55UJX/OouLX8Zypo/Wq9yoXh/b97ne/S9pXIVYKqt6z8ooIH6zEeS8AQU9QNTrsEvS661XH88ZKQ0xH79q1q3HknYsYRuzi52NFofy84xzfnT9nnZjGT33Oyy+/vPH+OAogJehty1T3dwFA0D8UXbd3F7dPdzn5TepzTiqOS19vywQg6Ky6CGCEObaBpkybxrHfMSqf5jbT/DmLYup83BFw1WPjdxw39nWPve222/wDAhD0dIcPH57o8RHquvh0DXOEshjLaUSt/JyTipWDLpskJnn/ABD0VjHl27S3d4wcU6a9Y3tw2+Ft8Vop29DbdhbrsgPbajxn005xYZo7xdmGDlDNiWUAwAh980k5aUyKGOFOazQZ08/TPkRpms85ran21fg9AYzQZ0TKlHebOJxsVkzjUqWTnMAGAEGvFIeQdT0pS1FsZ57k8Rvx/Zok6h/96EenshIFwKVmfso94hR7ncdUe9dTlY6zM9pGF7/zrbfeOtb7FSegMaUOIOhTFxf/mHQKeVrb3FdDft75aZ34ZVKxF3sc/z5t+YVcprE5ANg8Zu16BzMd9Di96Wbenjsro+H4PeNvKehAbr0MZAR9jcR0uSlgADYDx6EDgKADAIIOAAg6ACDoACDoAICgAwCCDgAIOgAIOgAg6ADA1M17C2DtLC4uLl8drsnOnTuzLVu2eLMAQYeuTpw4sXzFttUIaVxi99VXX13+eO7cuaTH7NixI/vYxz6W3XjjjcvLtZHkv2e8p3W/W/xOcXGkcZ//nXfeWX7+tssXx2vFClK81r59+6woIeiwmT333HPLwY3/2R86dGiqAf3Nb36TvfHGG50fF6P4WKa4zvunP/3p5SitZxHw559/fjm0qSst4eqrr16+ZnXK7xfPG+9nvEaX9zFu8Zi4nOYnP/nJ5dcEQYdNKMKZByPiG4GZhohxMeb5SDEfNdaNPmNaPj5GiGKZfvWrX2V33nln55WUuhFyili56RLaJ598slPIcxHa+F1TVloiyMWY5+9j3ePyWYK4xef5CsGuXbs23KwHCDp8iIrxiUDGLQ9M2ygzgpNva4+P8ZjUUXqEL19JmXTUHaFNWXHJYx4rLDfccENjNONnI+JHjx5dXuGJr2OZb7/99tbXKb6f8fz5czWJ9y3/mfj5eO9jGUHQgeQgliM2jedKCew05LMFbaPZfOUkRsz57EY+1d00AxDbtONn4nVSZhOm9X6OM5MAgg4sx6tqdF3c471qB7GmHcvqxM/nzxmB7TpVH4rb/btsgojXfeaZZ8Z6j9r2/C/Kd6obZwUFBB0YW8S8auo6ppnjFqrujwB1DWRxm33sJT+OeFz+PDHan9Y+BdMSI/txlunxxx/3jxFBBzaG2C6d279//1jPEXuBx6xCzBB02X4fP3PLLbd0eq3yjoOAoMNY8hFyLrYHR9DW+2FiVcrT7ZP8DjEKznesi+CmhDpWAroeW24aHAQdJhIhqTq0K75+6qmnlqd0Y0/oaZyIJF6rbbp3GtPBxZFuhH1aU8wx6u868l7tlbDyihgg6MyY/BCptsO64mdiOjhORDLuGc3W2rT2bi9LnXaPn+m6zT92DAQEHTqJ6fTYgzv1sKV8r+2Ygo+wjztaj+nvmMLuKmKXun25eFrZcV+v6fXjvWsL+qSH5qWKFaxxVrKM6hF02OAiTL/+9a/Hjk1+NrN8Gr6rOMQqHpsHMsJYdfrRCGLMHORBjtccZ4ex/PXyw97qDpurep9iJSY/LKzr66e+TtVrdg16/H4xIxGPjZ3/qg5jK98v6Ag6bGD5NHDVqDyiGnGIbenF78X//GO7cTE08fh8m3uM1ruKx8e2+Xw54qxo5ajHcubb9OO1J5nqj9cpPl8EsOlQr3zfgVzKGeLKqg7Pi6jG+xbLEytD5W3x424Pj+eNlbQQK0Gf+cxnOt0Pgg4bTH4O73wEGZGMkMax1vkUejHoEaUIX4QnIhcj1OKZzLqOJovBLK5UxNfloBd30IvXnCTo+Slmi8/XNgtRNOnrF2dGchHWfCVqUuWVra73g6DDBhPRjinXCHjXq2wVj6uOOEX0xgl6BCUiFs+XR7YqavG9PLyxzJOEqLy9v237f9ef7xrd4orGNIIef8tYQYj3qOrv2nY/CDpsMBHRcabIy2Iavuv28zyK+XW7Yzo6Pq87dWncHz+Xb4uOq6yNG9h4/MGDB5dnGPIrpzWJ3y3fmz3fhj/J1dryFZTiSkwsRzmuxdmTVPn+DHFa21jRqtpuH98r3j/p7wKCDjMsIplPZcf27C4j0+LOYl1PEpPvbZ7vHFf8XtuoNw9uecq+beUhnjtuMSouLmu8fn6IYMw6FC/aEsuUH2qX8t7EsuU7KE5yjP1GOQwRBB06KE9rT3N7a4QjIpbvKT7OXvYxcm27rGjZOMeDT+LGG2+8eAnV4v4IVSPrOimH2cXKQfHwvHFMaxs+CDqsIxG+4k5bIUaTKVPUqfIT08TIsu0a6EUxKo/t/hHLca4stpZi+WJzQWwi6LqPQbzXsZ9CynbuGPkXr22ecrx9+Vj+jXhKXxB0aFF3OFscRhWhGeeY87rRZ9fReSxX3fHVXWYIJlnZ6TISzrdZFy+9mvKYQ4cOTbQi0bbiNe6x/CDosIHke5/nF2OJr+P480mndcti1N81Kvlx5JMcPz3OseTFlZ2uKyExA9Hl94yVhpii73Ke+Ph75dv2U0f1+dED8fm4V6ADQYd17P7771/xvdU4xGktrt4W089NX48zui8GPeX5xtlU0XU5206QU7VMk6zYgKADF8XU8GpvB4/nv+uuuy5uw550BSKiWZyyT5m+H/cc64CgA6u44iDOsLH0vQUAIOgAgKADAIIOAAg6AAg6ACDoAICgAwCCDgCCDgAIOgAwbTN9LvfFxUX/AgAQ9I0sLmJx/Pjx5RsAm8vCwoKgz4r9+/f7Fw/ApmEbOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoACDoAICgAwCCDgAIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoACDoAICgAwCCDgAIOgAIOgAg6ACAoAMAgg4Agg4ACDoAIOgAgKADgKADAIIOAAg6ACDoAICgA4CgAwCCDgAIOgAg6AAg6ACAoAMAgg4ACDoACDoAIOgAgKADAIIOAIIOAAg6ACDoAICgA4CgAwCCDgAIOgAg6AAg6ACAoAMAgg4ACDoACDoAIOgAgKADAIIOAIIOAAg6ACDoAMA6CPrQWwoAU29ra1/7q/GkAMDGHqFnvV5P8AEgfXA8lW72a15obEtLS+eHw+EZfy8AuBDW4fDs+ZGKkE9tENw2Qm97sWH5Z959990To2V+058PAD5w5syZI0ePHj2Z2NNiV4dt3U0NetbyhCu+99JLLx0fLfhr/nwA8IE//vGPr0cfuw7sxx2hD6fxxA8//PCx0VrIk0tLS0f9CQGYdefPnz/229/+9qnHHnvs3ZqReGq4h0lB/2Bqv/WBw5bh//DEiRODn/70p794//33/y22GfhTAjCrRh08NxrkPvGTn/zkP0+fPj1o6GlT5IcNTb74+dwlde/3exc+LX6suhXv6xdWDuLW+/nPf37y5ptvfvvAgQO3zc/PX5k5gQ0As2dpcXHxxR//+Mffeeihh16rifag5vO2bekrtAU9qwh7VvP5Jbef/exn71955ZXPjKJ+9fbt2z/h7wrALHnvvff+/eGHH/7Ggw8++EpWP9U+KH3MSp9PHPSqiGeJo/eLtyeeeOLEkSNHnrnjjjuGCwsLn+r1ekbqAGxqw+Fw8Nprrz36la985eHvfe97RxtiXjcqHxQi3rbNfZhVjcTn5+frgl5161fc5i7cN1e+75FHHrn5c5/73IOXXXbZX8zNze32JwdgMxkMBqdPnTr13LPPPvvP9957739UjL6XCl8vXfjYdmsK/yUj+NSgZ6WQlz8Wg96vufXuueeenQ888MCNhw4duu+qq676m23btl1fMQMAABtmQH7u3Ln3jxw58vjhw4f/67vf/e5///KXv/zDmTNnlrLqKfVizIctYW/art4c9FLU26bW+wkj9RVRzz9eeeWVW7785S9fe/Dgwf3XXnvtvssvv3z3aOS+bTj8YJlGH7PSygUAfOgBP3/+/NnFxcVTR48efefpp59+5Uc/+tHbI+cKgS2Oqgc1ny+Vvr9U87N1I/RLYh5HqjUFvW6U3s/ap97znymHvfx52170og7Augp6xefDis8HFWEfVHw9aLi/bRv6JUGfKy9pxY5x5bhnDfe33VcX5V7Dm5RVrPW4AAwAaxXwYU20syxth7dBzfdSptNbd4YLg8Egmy8v+ajyw9IovfLBFd8bXBh5D2rC3i/FuzjaH7aM0I3OAVhvo/RhQ/QHLSP1Qc3oPeVc7uVuL3+cT1joYnCrvp/VrLEMKmJcDnmWNR/jnjK6B4C1DHlK1Oti3jR6bxupNw2wa4NeDHj58+Ioe1gYeQ9qAlx8bHlEPmgIfErMRR6A1Y53U9Sbpsjrdmyr2ou9yzbz4uh82Bb0ttF6Vgp7+RcdlGKcx7w8Qi+vNNTtYS/mAKyXsI+7Lb1utN7lBDK1+5DVRnF+fr58f+rhbFWHtlXtHZ+yh3vTCF3QAVjroDdtaq7a9JxyVriUneJWLEtxdD7JCL1XscZQvFBLVhqpV+341iXomZE6AB/iqLwu7qmHrlXtMNdlir1157jGGFaM0rOWUXRdnPuJP9c2MhdvANbTSD1lj/dhlj69nrIj3LDikuftgRwj6ll26SFqTVdmawu5oAOwnoNeF/O2r9um1TvFPDmQLVFPiXIv8WeqnlfQAVhvQW8bpWdZ2sloUp7novJ287ECmRj1LoFPGemLOQAbaaSeGve2gFe+Tt3ofKxI1pxFrml0nRLt1J3gRB2A9TJCb4txW7CHDSsGl7xGU8gnCmTNaL1rpKcVcZEHYC3i3WWknhL8uvsvappin1oMG6Je9f0uU/WiDcBGHamnBr71+VJG5VMP5RTjbkQOwEYbsbddvGzY4Tk7h3zqcbwQ9UmC3FvL5QWAKYe9ywi+8v5xY75qgWwZsYs4ALMc94lH4x9KLAsj92m+psgDsF7C3el5phnxdRPGDiN5ANgwuuydDgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADBr/l+AAQCL/KXCjY+nXAAAAABJRU5ErkJggg==';

    const MARGIN_PADDING_CONTROLS = [
        {
            label:'Top',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1.048" height="9.017" fill="#555d66"></rect><rect x="16.265" y="5.498" width="1.023" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.186" width="8.964" height="2.482" fill="#272b2f"></rect><rect x="5.487" y="16.261" width="9.026" height="1.037" fill="#555d66"></rect></svg>)
        },
        {
            label:'Right',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1.046" height="9.017" fill="#555d66"></rect><rect x="15.244" y="5.498" width="2.518" height="9.003" fill="#272b2f"></rect><rect x="5.518" y="2.719" width="8.964" height="0.954" fill="#555d66"></rect><rect x="5.487" y="16.308" width="9.026" height="0.99" fill="#555d66"></rect></svg>)
        },
        {
            label:'Bottom',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1" height="9.017" fill="#555d66"></rect><rect x="16.261" y="5.498" width="1.027" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.719" width="8.964" height="0.968" fill="#555d66"></rect><rect x="5.487" y="15.28" width="9.026" height="2.499" fill="#272b2f"></rect></svg>)
        },
        {
            label:'Left',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.202" y="5.492" width="2.503" height="9.017" fill="#272b2f"></rect><rect x="16.276" y="5.498" width="1.012" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.719" width="8.964" height="0.966" fill="#555d66"></rect><rect x="5.487" y="16.303" width="9.026" height="0.995" fill="#555d66"></rect></svg>)
        },
    ];

    const blockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M19 1H5c-1.1 0-1.99.9-1.99 2L3 15.93c0 .69.35 1.3.88 1.66L12 23l8.11-5.41c.53-.36.88-.97.88-1.66L21 3c0-1.1-.9-2-2-2zm-7 19.6l-7-4.66V3h14v12.93l-7 4.67zm-2.01-7.42l-2.58-2.59L6 12l4 4 8-8-1.42-1.42z"/></svg>
    );

    class AdvIconEdit extends Component {

        constructor() {
            super( ...arguments );
            this.state = {
                showPopup: false,
                currentItem: 0,
                iconSelected: '',
                selectedIcon: false,
                iconThemeSelected: 'outlined',
                selectedIconTheme: false,
            };
            this.togglePopup = this.togglePopup.bind(this);
            this.handleIcon = this.handleIcon.bind(this);
            this.handleIconTheme = this.handleIconTheme.bind(this);
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-icon'];

            // No override attributes of blocks inserted before
            if (attributes.changed !== true) {
                if (typeof currentBlockConfig === 'object' && currentBlockConfig !== null) {
                    Object.keys(currentBlockConfig).map((attribute) => {
                        if (typeof attributes[attribute] === 'boolean') {
                            attributes[attribute] = !!currentBlockConfig[attribute];
                        } else {
                            attributes[attribute] = currentBlockConfig[attribute];
                        }
                    });
                }

                // Finally set changed attribute to true, so we don't modify anything again
                setAttributes( { changed: true } );
            }
        }

        componentDidMount() {
            const { attributes, setAttributes, clientId } = this.props;
            const { blockIDX } = attributes;

            setAttributes( { blockIDX: `advgb-icon-${clientId}` } );
        }

        componentDidUpdate() {
            const {currentItem, iconSelected, selectedIcon, iconThemeSelected} = this.state;
            if(selectedIcon) {
                this.setState({
                    selectedIcon: false,
                    selectedIconTheme: false
                });
                this.updateItems(parseInt(currentItem), {icon: iconSelected, iconTheme: iconThemeSelected});
            }
        }

        handleIcon(iconValue) {
            this.setState({
                iconSelected: iconValue,
                selectedIcon: true,
            });
        }

        handleIconTheme(iconThemeValue) {
            this.setState({
                iconThemeSelected: iconThemeValue,
                selectedIconTheme: true,
            });
        }

        updateItems(idx, data) {
            const { attributes, setAttributes } = this.props;
            const { items } = attributes;

            const newItems = items.map( (item, index) => {
                if (idx === index) {
                    item = { ...item, ...data };
                }
                return item;
            } );

            setAttributes( { items: newItems } );
            this.setState( { searchedText: '' } )
        }

        getItemData(idx, dataName) {
            const { attributes } = this.props;
            const { items } = attributes;

            let data = '';

            items.map( (item, index) => {
                if (parseInt(idx) === index) {
                    for (let key in item){
                        if( (dataName === key) && item.hasOwnProperty(key) ) {
                            data = item[key];
                        }
                    }
                }
            } );

            return data;
        }

        togglePopup() {
            const {showPopup} = this.state;

            this.setState( {
                showPopup: !showPopup
            } );
        }

        render() {
            const { attributes, setAttributes } = this.props;
            const {
                blockIDX,
                items,
                numberItem,
                tAlign,
                isPreview
            } = attributes;

            const { showPopup, currentItem } = this.state;

            const blockWrapClass = [
                'advgb-icon-wrapper'
            ].filter( Boolean ).join( ' ' );

            const blockClass = [
                'advgb-icons',
            ].filter( Boolean ).join( ' ' );

            let i = 0;
            let j = 0;

            return (
                isPreview ?
                    <img alt={__('Advanced Icon', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        <BlockControls>
                            <AlignmentToolbar
                                value={ tAlign }
                                onChange={ ( value ) => setAttributes( { tAlign: value } ) }
                            />
                        </BlockControls>
                        <InspectorControls>
                            <PanelBody title={ __( 'Icon Count', 'advanced-gutenberg' ) }>
                                <RangeControl
                                    label={ __( 'Number of Icons', 'advanced-gutenberg' ) }
                                    min={ 1 }
                                    max={ 10 }
                                    value={ numberItem }
                                    onChange={ (value) => setAttributes( { numberItem: value } ) }
                                />
                            </PanelBody>
                            {items.map( (item, idx) => {
                                    i++;
                                    if (i > numberItem) return false;
                                    return (
                                        <Fragment>
                                            <PanelBody
                                                title={ __( `Icon ${i} Settings`, 'advanced-gutenberg' ) }
                                                initialOpen={ false }
                                            >

                                                <BaseControl
                                                    label={ __( 'Icon Library (Material Icon)', 'advanced-gutenberg' )}
                                                >
                                                    <Button
                                                        className="button button-large advgb-browse-image-btn"
                                                        data-currentItem={idx}
                                                        onClick={ (event) => {
                                                            if(!showPopup) {
                                                                this.togglePopup();
                                                                this.setState({ currentItem: event.target.attributes.getNamedItem('data-currentItem').value });
                                                            }
                                                        }
                                                        }
                                                    >
                                                        { __( 'Icon Selection', 'advanced-gutenberg' ) }
                                                    </Button>
                                                </BaseControl>

                                                <SelectControl
                                                    label={ __('Icon Style', 'advanced-gutenberg') }
                                                    value={ item.style }
                                                    options={ [
                                                        { label: __('Default', 'advanced-gutenberg'), value: 'default' },
                                                        { label: __('Stacked', 'advanced-gutenberg'), value: 'stacked' },
                                                    ] }
                                                    onChange={ ( value ) => this.updateItems(idx, { style: value } ) }
                                                />
                                                <RangeControl
                                                    label={ __( 'Icon Size', 'advanced-gutenberg' ) }
                                                    min={ 10 }
                                                    max={ 250 }
                                                    value={ item.size }
                                                    onChange={ (value) => this.updateItems(idx, { size: value } ) }
                                                />
                                                <AdvColorControl
                                                    label={ __('Icon Color', 'advanced-gutenberg') }
                                                    value={ item.color }
                                                    onChange={ (value) => this.updateItems(idx, { color: value } ) }
                                                />
                                                <AdvColorControl
                                                    label={ __('Icon Background', 'advanced-gutenberg') }
                                                    value={ item.bgColor }
                                                    onChange={ (value) => this.updateItems(idx, { bgColor: value } ) }
                                                />
                                                {item.style && item.style === 'stacked' &&
                                                <Fragment>
                                                    <BaseControl
                                                        label={ __( 'Border', 'advanced-gutenberg' ) }
                                                        className="advgb-control-header"
                                                    />
                                                    <AdvColorControl
                                                        label={__( 'Border Color', 'advanced-gutenberg' )}
                                                        value={item.borderColor}
                                                        onChange={( value ) => this.updateItems( idx, { borderColor: value } )}
                                                    />
                                                    <RangeControl
                                                        label={__( 'Border Size(px)', 'advanced-gutenberg' )}
                                                        min={0}
                                                        max={20}
                                                        value={item.borderSize}
                                                        onChange={( value ) => this.updateItems( idx, { borderSize: value } )}
                                                    />
                                                    <RangeControl
                                                        label={__( 'Border Radius(%)', 'advanced-gutenberg' )}
                                                        min={0}
                                                        max={50}
                                                        value={item.borderRadius}
                                                        onChange={( value ) => this.updateItems( idx, { borderRadius: value } )}
                                                    />
                                                </Fragment>
                                                }
                                                <BaseControl
                                                    label={ __( 'Link', 'advanced-gutenberg' ) }
                                                    className="advgb-control-header"
                                                />
                                                <BaseControl
                                                    label={ __( 'Link', 'advanced-gutenberg' ) }
                                                >
                                                    <URLInput
                                                        value={item.link}
                                                        onChange={ (value) =>  this.updateItems(idx, { link: value } ) }
                                                        autoFocus={false}
                                                        isFullWidth
                                                        hasBorder
                                                    />
                                                </BaseControl>
                                                <SelectControl
                                                    label={ __('Link Target', 'advanced-gutenberg') }
                                                    value={ item.linkTarget }
                                                    options={ [
                                                        { label: __('Same Window', 'advanced-gutenberg'), value: '_self' },
                                                        { label: __('New Window', 'advanced-gutenberg'), value: '_blank' },
                                                    ] }
                                                    onChange={ ( value ) => this.updateItems(idx, { linkTarget: value } ) }
                                                />
                                                <TextControl
                                                    label={ __('Title for Accessibility', 'advanced-gutenberg') }
                                                    value={ item.title }
                                                    onChange={ ( value ) => this.updateItems(idx, { title: value } ) }
                                                />
                                                {item.style && item.style === 'stacked' &&
                                                <Fragment>
                                                    <BaseControl
                                                        label={ __( 'Padding', 'advanced-gutenberg' ) }
                                                        className="advgb-control-header"
                                                    />
                                                    <div className="advgb-controls-title">
                                                        <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                                        <div className="advgb-unit-wrapper" key="unit">
                                                            {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                                                <span
                                                                    className={`advgb-unit ${item.paddingUnit === unit ? 'selected' : ''}`}
                                                                    key={uIdx}
                                                                    onClick={() => this.updateItems( idx, { paddingUnit: unit } )}
                                                                >
                                                                    {unit}
                                                                </span>
                                                            ) )}
                                                        </div>
                                                    </div>
                                                    {
                                                        MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                                            <RangeControl
                                                                className="advgb-padding-margin-control"
                                                                key={mpIdx}
                                                                label={pos.icon}
                                                                beforeIcon={pos.icon}
                                                                value={item['padding' + pos.label]}
                                                                min={0}
                                                                max={180}
                                                                onChange={( value ) => this.updateItems( idx, { ['padding' + pos.label]: value } )}
                                                            />
                                                        ) )
                                                    }
                                                </Fragment>
                                                }
                                                <BaseControl
                                                    label={ __( 'Margin', 'advanced-gutenberg' ) }
                                                    className="advgb-control-header"
                                                />
                                                <div className="advgb-controls-title">
                                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                                    <div className="advgb-unit-wrapper" key="unit">
                                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                                            <span
                                                                className={`advgb-unit ${item.marginUnit === unit ? 'selected' : ''}`}
                                                                key={uIdx}
                                                                onClick={() => this.updateItems( idx, { marginUnit: unit } )}
                                                            >
                                                                    {unit}
                                                                </span>
                                                        ) )}
                                                    </div>
                                                </div>
                                                {
                                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                                        <RangeControl
                                                            className="advgb-padding-margin-control"
                                                            key={mpIdx}
                                                            label={pos.icon}
                                                            beforeIcon={pos.icon}
                                                            value={item['margin' + pos.label]}
                                                            min={0}
                                                            max={180}
                                                            onChange={( value ) => this.updateItems( idx, { ['margin' + pos.label]: value } )}
                                                        />
                                                    ) )
                                                }
                                            </PanelBody>
                                        </Fragment>
                                    );
                                }
                            )}
                        </InspectorControls>
                        <div className={blockWrapClass} id={blockIDX}>
                            <div className={ blockClass } style={ {textAlign: tAlign} }>
                                {items.map( (item, idx) => {
                                    j++;
                                    if (j > numberItem) return false;
                                    const advgbIconClass = [
                                        `advgb-icon-style-${item.style}`,
                                        'advgb-icon-wrap',
                                        `advgb-item-${idx}`,
                                    ].filter( Boolean ).join( ' ' );

                                    const iconWrapClass = [
                                        'advgb-icon',
                                        `advgb-icon-${item.icon}`
                                    ].filter( Boolean ).join(' ');

                                    const iconClass = [
                                        item.iconType === 'material' && 'material-icons',
                                        item.iconTheme !== '' && `-${item.iconTheme}`
                                    ].filter( Boolean ).join('');

                                    const iconWrapStyles = {
                                        display: 'flex',
                                        alignItems: 'center',
                                        marginTop: item.marginTop + item.marginUnit,
                                        marginBottom: item.marginBottom + item.marginUnit,
                                        marginLeft: item.marginLeft + item.marginUnit,
                                        marginRight: item.marginRight + item.marginUnit,
                                        paddingTop: item.style !== 'default' ? item.paddingTop + item.paddingUnit : 0,
                                        paddingBottom: item.style !== 'default' ? item.paddingBottom + item.paddingUnit : 0,
                                        paddingLeft: item.style !== 'default' ? item.paddingLeft + item.paddingUnit : 0,
                                        paddingRight: item.style !== 'default' ? item.paddingRight + item.paddingUnit : 0,
                                        borderWidth: item.style !== 'default' ? item.borderSize + 'px' : 0,
                                        borderStyle: 'solid',
                                        borderColor: item.borderColor,
                                        background: item.bgColor,
                                        borderRadius: item.borderRadius + '%'
                                    };

                                    const iconStyles = {
                                        fontSize: item.size + 'px',
                                        color: item.color
                                    };

                                    return (
                                        <Fragment>
                                            <div className={advgbIconClass}>
                                                <div className={iconWrapClass} style={iconWrapStyles}>
                                                    <i className={iconClass} style={iconStyles}>{item.icon}</i>
                                                </div>
                                            </div>
                                        </Fragment>
                                    );
                                })}
                            </div>
                            {
                                showPopup ?
                                    <IconListPopupHook
                                        content='iconpopup'
                                        closePopup={ () => {
                                            if(showPopup) {
                                                this.togglePopup();
                                            }
                                        } }
                                        onSelectIcon={ this.handleIcon }
                                        onSelectIconTheme={ this.handleIconTheme }
                                        selectedIcon={this.getItemData(currentItem, 'icon')}
                                        selectedIconTheme={this.getItemData(currentItem, 'iconTheme')}
                                    />
                                    :
                                    null
                            }
                        </div>
                    </Fragment>
            );
        }
    }

    const blockAttrs = {
        blockIDX: {
            type: 'string',
        },
        items: {
            type: 'array',
            default: times(10, () => (
                {
                    icon: 'beenhere',
                    iconType: 'material',
                    iconTheme: 'outlined',
                    size: 120,
                    color: '#111111',
                    style: 'default',
                    bgColor: '',
                    borderColor: '#111',
                    borderSize: 2,
                    borderRadius: 0,
                    paddingTop: 20,
                    paddingBottom: 20,
                    paddingLeft: 20,
                    paddingRight: 20,
                    marginTop: 0,
                    marginBottom: 0,
                    marginLeft: 0,
                    marginRight: 40,
                    paddingUnit: 'px',
                    marginUnit: 'px',
                    link: '',
                    linkTarget: '_self',
                    title: ''
                }
            ))
        },
        numberItem: {
            type: 'number',
            default: 2,
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        tAlign: {
            type: 'string',
            default: 'center',
        },
        isPreview: {
            type: 'boolean',
            default: false,
        }
    };

    registerBlockType( 'advgb/icon', {
        title: __( 'Advanced Icon', 'advanced-gutenberg' ),
        description: __( 'Advanced icon block with more options and styles.', 'advanced-gutenberg' ),
        icon: {
            src: blockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'icon', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        supports: {
            align: ["left", "center", "right"],
        },
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: AdvIconEdit,
        save: ( { attributes } ) => {
            const {
                blockIDX,
                className,
                items,
                numberItem
            } = attributes;

            const blockWrapClass = [
                'wp-block-advgb-icon',
                'icon-wrapper',
                className,
                blockIDX
            ].filter( Boolean ).join( ' ' );

            const blockClass = [
                'advgb-icons',
            ].filter( Boolean ).join( ' ' );

            let i = 0;
            return (
                <Fragment>
                    <div className={blockWrapClass}>
                        <div className={ blockClass }>
                            {items.map( (item, idx) => {
                                i++;
                                if (i > numberItem) return false;

                                let itemLink = item.link;
                                if (!item.link.match(/^[a-zA-Z]+:\/\//))
                                {
                                    itemLink = 'http://' + item.link;
                                }
                                const advgbIconClass = [
                                    `advgb-icon-style-${item.style}`,
                                    'advgb-icon-wrap',
                                    `advgb-item-${idx}`,
                                ].filter( Boolean ).join( ' ' );

                                const iconWrapClass = [
                                    'advgb-icon',
                                    `advgb-icon-${item.icon}`
                                ].filter( Boolean ).join(' ');

                                const iconClass = [
                                    item.iconType === 'material' && 'material-icons',
                                    item.iconTheme !== '' && `-${item.iconTheme}`
                                ].filter( Boolean ).join('');

                                return (
                                    <Fragment>
                                        <div className={advgbIconClass}>
                                            {item.link !== '' && <a href={itemLink} target={item.linkTarget} title={item.title} rel="noopener noreferrer">
                                                <span className={iconWrapClass}>
                                                    <i className={iconClass}>{item.icon}</i>
                                                </span>
                                            </a>
                                            }
                                            {item.link === '' &&
                                            <span className={iconWrapClass}>
                                                <i className={iconClass}>{item.icon}</i>
                                            </span>
                                            }
                                        </div>
                                    </Fragment>
                                );
                            })}
                        </div>
                    </div>
                </Fragment>
            )
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: ( { attributes } ) => {
                    const {
                        blockIDX,
                        items,
                        numberItem
                    } = attributes;

                    const blockWrapClass = [
                        'wp-block-advgb-icon',
                        'icon-wrapper',
                        blockIDX
                    ].filter( Boolean ).join( ' ' );

                    const blockClass = [
                        'advgb-icons',
                    ].filter( Boolean ).join( ' ' );

                    let i = 0;
                    return (
                        <Fragment>
                            <div className={blockWrapClass}>
                                <div className={ blockClass }>
                                    {items.map( (item, idx) => {
                                        i++;
                                        if (i > numberItem) return false;
                                        const advgbIconClass = [
                                            `advgb-icon-style-${item.style}`,
                                            'advgb-icon-wrap',
                                            `advgb-item-${idx}`,
                                        ].filter( Boolean ).join( ' ' );

                                        const iconWrapClass = [
                                            'advgb-icon',
                                            `advgb-icon-${item.icon}`
                                        ].filter( Boolean ).join(' ');

                                        const iconClass = [
                                            item.iconType === 'material' && 'material-icons',
                                            item.iconTheme !== '' && `-${item.iconTheme}`
                                        ].filter( Boolean ).join('');

                                        return (
                                            <Fragment>
                                                <div className={advgbIconClass}>
                                                    {item.link !== '' && <a href={item.link} title={item.title}>
                                                        <div className={iconWrapClass}>
                                                            <i className={iconClass}>{item.icon}</i>
                                                        </div>
                                                    </a>
                                                    }
                                                    {item.link === '' &&
                                                    <div className={iconWrapClass}>
                                                        <i className={iconClass}>{item.icon}</i>
                                                    </div>
                                                    }
                                                </div>
                                            </Fragment>
                                        );
                                    })}
                                </div>
                            </div>
                        </Fragment>
                    )
                },
            },
            {
                attributes: blockAttrs,
                save: ( { attributes } ) => {
                    const {
                        blockIDX,
                        items,
                        numberItem
                    } = attributes;

                    const blockWrapClass = [
                        'wp-block-advgb-icon',
                        'icon-wrapper',
                    ].filter( Boolean ).join( ' ' );

                    const blockClass = [
                        'advgb-icons',
                    ].filter( Boolean ).join( ' ' );

                    let i = 0;
                    return (
                        <Fragment>
                            <div className={blockWrapClass} id={blockIDX}>
                                <div className={ blockClass }>
                                    {items.map( (item, idx) => {
                                        i++;
                                        if (i > numberItem) return false;
                                        const advgbIconClass = [
                                            `advgb-icon-style-${item.style}`,
                                            'advgb-icon-wrap',
                                            `advgb-item-${idx}`,
                                        ].filter( Boolean ).join( ' ' );

                                        const iconWrapClass = [
                                            'advgb-icon',
                                            `advgb-icon-${item.icon}`
                                        ].filter( Boolean ).join(' ');

                                        const iconClass = [
                                            item.iconType === 'material' && 'material-icons',
                                            item.iconTheme !== '' && `-${item.iconTheme}`
                                        ].filter( Boolean ).join('');

                                        return (
                                            <Fragment>
                                                <div className={advgbIconClass}>
                                                    {item.link !== '' && <a href={item.link} title={item.title}>
                                                        <div className={iconWrapClass}>
                                                            <i className={iconClass}>{item.icon}</i>
                                                        </div>
                                                    </a>
                                                    }
                                                    {item.link === '' &&
                                                    <div className={iconWrapClass}>
                                                        <i className={iconClass}>{item.icon}</i>
                                                    </div>
                                                    }
                                                </div>
                                            </Fragment>
                                        );
                                    })}
                                </div>
                            </div>
                        </Fragment>
                    )
                }
            }
        ]
    });
}) ( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );