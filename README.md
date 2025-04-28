
# Projeto: Sistema de Filtro de Imóveis com Elementor + Search & Filter (WordPress)

Este projeto implementa um sistema de busca com filtros para um site de imóveis WordPress, utilizando:

- Elementor Pro
- Search & Filter Free
- Pods Admin
- ACF Free
- Tema Hello Elementor

## Funcionalidades

- Filtros por tipo de imóvel, número de quartos e status
- Exibição em grid (3 por linha)
- Paginação dinâmica na mesma página
- Reutilização de template do Elementor
- Exibição de feedback de filtros aplicados
- Ocultamento de rascunhos e privados

## Como usar

1. Suba o conteúdo do `functions-snippet.php` no seu `functions.php` ou via plugin de snippets (Faz um backup antes).
2. Crie uma página e adicione o shortcode `[mostrar_imoveis_loop]` abaixo do formulário do Search & Filter.
3. Se certifica que o ID do template do Elementor estará correto.
4. Adiciona os estilos de `filtros-css.css` no seu tema (Vai de acordo com a preferência).

## Shortcode Search & Filter

```
[searchandfilter 
  fields="search,tipo_imovel,quartos_padrao,status_do_imovel" 
  types="text,select,select,select" 
  headings=",,,," 
  post_types="imovel" 
  submit_label="Buscar" 
  class="filtro-imovel-ui"]
```
