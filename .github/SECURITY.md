# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 0.1.x   | Yes       |

## Reporting a Vulnerability

If you discover a security vulnerability, **do not** open a public issue. Email **dev@symfinity.net** with:

- Type of vulnerability
- Full paths of source file(s) related to the issue
- The location of the affected code (tag, branch, commit, or URL)
- Step-by-step reproduction instructions
- Proof-of-concept or exploit code (if possible)
- Impact and plausible attack scenario

We aim to acknowledge within 48 hours and provide a detailed response within 7 days.

## Security best practices

When using this bundle:

1. Keep Symfony and Symfinity dependencies updated
2. Install with `--dev` only — **do not** enable UI Profiler in production
3. Disable the Web Debug Toolbar and profiler in production environments
4. Treat profiler output as sensitive (routes, parameters, headers, session data)

## Security contact

**dev@symfinity.net**
