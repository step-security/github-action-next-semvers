[![StepSecurity Maintained Action](https://raw.githubusercontent.com/step-security/maintained-actions-assets/main/assets/maintained-action-banner.png)](https://docs.stepsecurity.io/actions/stepsecurity-maintained-actions)

# Next SemVers

GitHub Action that output the next version for major, minor, and patch version based on the given semver version.


## Options

This action supports the following options.

### version

The version we want to have the next versions for.

* *Required*: `Yes`
* *Type*: `string`
* *Example*: `v1.2.3` or `1.2.3`

### strict

Strict version validation, when turned off, the version is suffixed with `.0` until it contains 3 x `.`.

* *Required*: `No`
* *Type*: `string`
* *Example*: `true` or `false`

## Output

This action output 6 slightly different outputs. A new major, minor, and patch version and a variant of those prefixed
with a `v`. For example when you input `1.2.3` it will give you the following outputs:

* `major`: `2.0.0`
* `minor`: `1.3.0`
* `patch`: `1.2.4`
* `v_major`: `v2.0.0`
* `v_minor`: `v1.3.0`
* `v_patch`: `v1.2.4`

In addition, if your input contains an indicator that it is a pre-release (e.g., `1.2.3-alpha`), the output for `patch` version changes accordingly (while the behaviour for `major` and `minor` are not affected and work as usual):

* `major`: `2.0.0`
* `minor`: `1.3.0`
* `patch`: `1.2.3`
* `v_major`: `v2.0.0`
* `v_minor`: `v1.3.0`
* `v_patch`: `v1.2.3`


## Example

The following example works together with the [`step-security/github-action-get-previous-tag`](https://github.com/step-security/github-action-get-previous-tag)
and [`WyriHaximus/github-action-create-milestone`](https://github.com/marketplace/actions/create-milestone) actions.
Where it uses the output from that action to supply a set of versions for the next action, which creates a new
milestone.

```yaml
name: Generate
jobs:
  generate:
    steps:
      - uses: actions/checkout@v7
      - name: 'Get Previous tag'
        id: previoustag
        uses: "step-security/github-action-get-previous-tag@v2"
        env:
          GITHUB_TOKEN: "${{ secrets.GITHUB_TOKEN }}"
      - name: 'Get next minor version'
        id: semvers
        uses: "step-security/github-action-next-semvers@v1"
        with:
          version: ${{ steps.previoustag.outputs.tag }}
      - name: 'Create new milestone'
        id: createmilestone
        uses: "WyriHaximus/github-action-create-milestone@v1"
        with:
          title: ${{ steps.semvers.outputs.patch }}
        env:
          GITHUB_TOKEN: "${{ secrets.GITHUB_TOKEN }}"
```

## License ##

[MIT](LICENSE)
