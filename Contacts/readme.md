#  Create Contacts In Magento 2


## Goal
- Create New AttributesSet Using Setup db script.

![](docs/attributeSet.png)

## Step By Step Tutorials

- [app/code/Bdcrops/Contacts/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_Contacts',
            __DIR__
        );
    ```
    </details>



## Ref

- [techjeffyu](http://techjeffyu.com/blog/magento-2-create-uicomponent-list-and-uicomponent-form-by-example)
- [git.pablomalo.fr](https://git.pablomalo.fr/polo/jeff-contacts/tree/master)
